#!/bin/bash

# PHP Ollama Web UI 启动脚本 (Linux/Mac)
# 在终端中运行: ./run_server.sh

# 设置颜色
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}========================================${NC}"
echo -e "${CYAN}     PHP Ollama Web UI 启动器${NC}"
echo -e "${CYAN}========================================${NC}"
echo ""

# 检查 PHP
echo -n "[1/3] 检查 PHP 是否已安装... "
if command -v php &> /dev/null; then
    echo -e "${GREEN}[✓] PHP 已安装${NC}"
else
    echo -e "${RED}[✗] PHP 未安装${NC}"
    echo "请先安装 PHP"
    exit 1
fi

echo ""

# 检查 Ollama
echo -n "[2/3] 检查 Ollama 服务器状态... "
if curl -s http://localhost:11434/api/tags &> /dev/null; then
    echo -e "${GREEN}[✓] Ollama 服务器正在运行${NC}"
else
    echo -e "${YELLOW}[⚠] Ollama 服务器未运行${NC}"
    echo "正在尝试启动 Ollama..."
    export OLLAMA_KEEP_ALIVE=-1    # 模型永久驻留
    export OLLAMA_HOST=0.0.0.0:11434
    nohup ollama serve > /tmp/ollama.log 2>&1 &
    sleep 5
fi

echo ""

# 启动 PHP 服务器
echo -e "${CYAN}[3/3] 启动 PHP Web 服务器...${NC}"
echo -e "${GREEN}访问地址: http://localhost:8080/${NC}"
echo -e "${YELLOW}按 Ctrl+C 停止服务器${NC}"
echo ""

# 启动服务器
php -S 0.0.0.0:8080 index.php
