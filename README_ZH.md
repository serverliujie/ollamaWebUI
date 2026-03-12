# PHP 版本 Ollama Web UI

这是一个基于 PHP 的 Web 界面，用于与 Ollama AI 模型进行交互。

## 功能特点

- ✅ **模型列表** - 显示所有可用的 Ollama 模型
- ✅ **单次对话** - 每次对话独立，不保留历史
- ✅ **连续对话** - 保留对话历史，支持多轮对话
- ✅ **流式响应** - 实时显示 AI 回复，无需等待完整响应
- ✅ **模型卸载** - 切换模型时自动卸载旧模型，释放内存
- ✅ **中文支持** - 完整支持中文输入输出

## 文件说明

### index.php
主 PHP 文件，包含：
- API 端点处理（`/api/tags`, `/api/generate`, `/api/chat`, `/api/unload-model`）
- Web 界面（HTML + CSS + JavaScript）
- UTF-8 编码处理（支持中文字符）

### run.sh
启动脚本，用于启动 PHP 开发服务器

### test.php
测试脚本，用于验证所有功能是否正常工作

## API 端点说明

### 1. 获取模型列表
- **路由**: `GET /api/tags`
- **功能**: 从 Ollama 服务器获取所有可用的模型列表
- **返回**: JSON 格式的模型列表

### 2. 生成回复（单次对话）
- **路由**: `POST /api/generate`
- **功能**: 向 Ollama 发送提示词，获取流式回复
- **请求体**:
  ```json
  {
    "model": "模型名称",
    "prompt": "提示词",
    "stream": true
  }
  ```
- **返回**: NDJSON 格式的流式回复

### 3. 聊天对话（连续对话）
- **路由**: `POST /api/chat`
- **功能**: 向 Ollama 发送对话历史，获取流式回复
- **请求体**:
  ```json
  {
    "model": "模型名称",
    "messages": [
      {"role": "user", "content": "用户消息"}
    ],
    "stream": true
  }
  ```
- **返回**: NDJSON 格式的流式回复

### 4. 卸载模型
- **路由**: `POST /api/unload-model`
- **功能**: 卸载指定的模型，释放内存资源
- **请求体**:
  ```json
  {
    "model": "模型名称"
  }
  ```
- **返回**: 操作状态

## 使用方法

### 1. 启动服务器
```bash
# Windows
php -S 0.0.0.0:8080 index.php

# Linux/Mac
./run.sh
```

### 2. 访问 Web 界面
打开浏览器，访问 `http://localhost:8080/`

### 3. 运行测试
```bash
php test.php
```

## 技术实现

### PHP 部分
- 使用 PHP 内置开发服务器
- 使用 cURL 代理请求到 Ollama 服务器
- 处理 UTF-8 编码，支持中文字符
- 流式响应使用 `CURLOPT_WRITEFUNCTION`

### Web 界面部分
- 响应式设计，支持移动端
- 实时流式显示回复
- 支持对话历史保存到 localStorage
- 支持语音朗读（TTS）
- 支持代码复制和折叠

## 常见问题

### 1. 中文显示乱码
确保 PHP 和浏览器都使用 UTF-8 编码。代码中已经处理了 UTF-8 编码问题。

### 2. 流式响应不工作
检查 Ollama 服务器是否正常运行，以及网络连接是否正常。

### 3. 模型切换失败
确保模型名称正确，并且 Ollama 服务器中有该模型。

## 环境要求

- PHP 7.4 或更高版本
- cURL 扩展
- mbstring 扩展（推荐）
- Ollama 服务器运行中

## 许可证

MIT License
