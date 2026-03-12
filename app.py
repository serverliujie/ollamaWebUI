from flask import Flask, request, jsonify, send_from_directory, Response
import requests
import os
import subprocess

app = Flask(__name__)
OLLAMA_BASE = os.environ.get('OLLAMA_BASE', 'http://localhost:11434')

@app.route('/')
def index():
    return send_from_directory('.', 'index.html')

@app.route('/api/tags', methods=['GET'])
def tags():
    try:
        resp = requests.get(f'{OLLAMA_BASE}/api/tags')
        return jsonify(resp.json())
    except:
        return jsonify({"models": []})

@app.route('/api/generate', methods=['POST'])
def generate():
    try:
        data = request.json
        def stream():
            try:
                resp = requests.post(
                    f"{OLLAMA_BASE}/api/generate",
                    json=data,
                    stream=True
                )
                for chunk in resp.iter_content(chunk_size=None):
                    yield chunk
            except:
                yield b'{"done":true}'
        return Response(stream(), mimetype='application/x-ndjson')
    except:
        return jsonify({"error": "error"})

@app.route('/api/chat', methods=['POST'])
def chat():
    try:
        data = request.json
        def stream():
            try:
                resp = requests.post(
                    f"{OLLAMA_BASE}/api/chat",
                    json=data,
                    stream=True
                )
                for chunk in resp.iter_content(chunk_size=None):
                    yield chunk
            except:
                yield b'{"done":true}'
        return Response(stream(), mimetype='application/x-ndjson')
    except:
        return jsonify({"error": "error"})

@app.route('/api/unload-model', methods=['POST'])
def unload_model():
    try:
        data = request.json
        model_name = data.get('model')
        if not model_name:
            return jsonify({'error': '模型名称不能为空'}), 400
        
        resp = requests.post(
            f'{OLLAMA_BASE}/api/generate',
            json={
                'model': model_name,
                'keep_alive': 0,
                'prompt': ''
            }
        )
        return jsonify({'status': 'success', 'model': model_name})
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080, debug=True)