#!/bin/bash

# Название контейнера с PostgreSQL
DB_CONTAINER=$(docker-compose ps -q db)

# Выполняем SQL-запросы
cat add_missing_columns.sql | docker exec -i $DB_CONTAINER psql -U postgres forum

echo "SQL-скрипт выполнен!" 