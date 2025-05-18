#!/bin/bash

# Script to add custom hostnames to /etc/hosts for easier access

HOSTS=(
  "bootcamp.local"
  "lab1.evilcorp.local"
  "lab2.evilcorp.local"
  "lab3.evilcorp.local"
  "lab4.evilcorp.local"
)

echo "🔍 Checking current /etc/hosts entries..."

for HOST in "${HOSTS[@]}"; do
  if grep -q "$HOST" /etc/hosts; then
    echo "✅ $HOST already exists in /etc/hosts"
  else
    echo "🚀 Adding $HOST to /etc/hosts..."
    echo "127.0.0.1 $HOST" | sudo tee -a /etc/hosts > /dev/null
  fi
done

echo ""
echo "🎉 Host setup complete!"
echo "You can now access:"
for HOST in "${HOSTS[@]}"; do
  echo "🔗 http://$HOST"
done
