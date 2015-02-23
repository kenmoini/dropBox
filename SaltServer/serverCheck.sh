#!/usr/bin/env bash

# EDIT: Change path to where Salt Server PID file is located, same path as main server script
PIDFILE="/opt/saltServer/saltServer.pid"

if [ -e "${PIDFILE}" ] && (ps -u $USER -f | grep "[ ]$(cat ${PIDFILE})[ ]"); then
  echo "Already running."
  exit 99
fi

# EDIT: Change path to where Salt Server script is located
nohup /opt/saltServer/saltServer.py >/dev/null 2>&1&
