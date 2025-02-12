#!/bin/bash
#
# vim:ft=sh
# 5 5 * * * /home/al/w/jiu/cron.sh

############### Variables ###############

############### Functions ###############

############### Main Part ###############

pdir=$(dirname $0)
. $pdir/.env.local

# DATABASE_URL="mysql://root:toor@127.0.0.1:3306/jiu?serverVersion=mariadb-10.5.18&charset=utf8mb4"
# DATABASE_URL="postgresql://datong:datong@127.0.0.1:5432/datong?serverVersion=16&charset=utf8"
# echo $DATABASE_URL
t=${DATABASE_URL#*//} # root:toor@127.0.0.1:3306/jiu?serverVersion=mariadb-10.5.18&charset=utf8mb4
user=${t%%:*}
# echo $user
tt=${t%%@*} # root:toor
passwd=${tt##*\:}
# echo $passwd
tt=${t%%\?*} # root:toor@127.0.0.1:3306/jiu
db=${tt##*/}
# echo $db
tt=${t#*@} # 127.0.0.1:3306/jiu
host=${tt%\:*}
# echo $host
tt=${tt#*\:} # 3306/jiu
port=${tt%/*}
# echo $port

echo dump db...
echo User: $user
echo DB: $db
echo host: $host
echo port: $port

dir=~/w/db.$db
mkdir -p $dir; cd $dir
git status &> /dev/null
[ "$?" -eq 128 ] && git init
# mysqldump --skip-extended-insert -u$user -p$passwd -h $host -P $port $db  > db.sql
pg_dump -U $user -w -h $host -p $port $db > db.sql
echo -- $(date) >> db.sql

git add .
git commit -m "db dump" --no-gpg-sign > /dev/null
git push &> /dev/null
