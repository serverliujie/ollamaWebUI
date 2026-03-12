#!/bin/bash

# 检查 ollama 是否正在运行
if pgrep -x "ollama" > /dev/null; then 
  echo "ollama 已经在运行中"; 
else 
  export OLLAMA_KEEP_ALIVE=-1    # 模型永久驻留
  export OLLAMA_HOST=0.0.0.0:11434
  nohup ollama serve > /tmp/ollama.log 2>&1 &
  nohup php -S 0.0.0.0:8080 /opt/ollama-webui/index.php > /dev/null 2>&1 &
  echo "ollama 启动中"; 
  
fi
