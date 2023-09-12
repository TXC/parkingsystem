#!/usr/bin/env bash

MYCMD="/bin/false"
if [ -f "/usr/bin/mysql" ]; then
  MYCMD="/usr/bin/mysql"
elif [ -f "/usr/bin/mariadb" ]; then
  MYCMD="/usr/bin/mariadb"
fi;

if [ ! -f "${HOME}/.my.cnf" ]; then
  echo "No my.cnf found. Creating";
  echo "[client]
user=root
password="${MYSQL_ROOT_PASSWORD}"
host="${DATABASE_HOST}"
port="${DATABASE_PORT}"

[mysql]
user=root
password="${MYSQL_ROOT_PASSWORD}"
host="${DATABASE_HOST}"
port="${DATABASE_PORT}"

[mysqldump]
user=root
password="${MYSQL_ROOT_PASSWORD}"
host="${DATABASE_HOST}"
port="${DATABASE_PORT}"

[mysqldiff]
user=root
password="${MYSQL_ROOT_PASSWORD}"
host="${DATABASE_HOST}"
port="${DATABASE_PORT}"
" > ${HOME}/.my.cnf
fi

# Test we can access the db container allowing for start
for i in {1..50}; do
  $MYCMD -e "show databases" && s=0 && break || s=$? && sleep 2;
done
if [ ! $s -eq 0 ]; then
  >&2 echo 'Unable to connect to database'
  exit $s;
fi

# Set configured timezone
$MYCMD -e "SET time_zone = '"${TZ}"'"

# Init some stuff in db before leaving the floor to the application
echo "Creating "${DATABASE_NAME}" database";
$MYCMD -e "CREATE DATABASE "${DATABASE_NAME}""

echo "Setting up user \""${DATABASE_USER}"\"@\"%\" ...";
CREATE="CREATE USER '"${DATABASE_USER}"'@'%' IDENTIFIED BY '"${DATABASE_PASSWORD}"';";

echo "Granting permissions for \""${DATABASE_NAME}"\".* \""${DATABASE_USER}"\"@\"%\" ..."
PERMS='ALTER, CREATE, CREATE TEMPORARY TABLES, DELETE, DROP, INDEX, INSERT, LOCK TABLES, REFERENCES, SELECT, UPDATE';
QUERY="GRANT "${PERMS}" ON \`"${DATABASE_NAME}"\`.* TO '"${DATABASE_USER}"'@'%';";

$MYCMD -e "${CREATE}"
$MYCMD -e "${QUERY}"

echo 'All done!'
