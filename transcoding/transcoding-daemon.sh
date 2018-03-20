#!/bin/bash
INDIR="/var/www/html/incoming/"
OUTDIR="/var/www/html/encoded/"

FFMPEG="/root/bin/ffmpeg -y"

LOG="/var/log/$(basename $0).log"
touch $LOG

# перенаправляем весь вывод в лог
exec >> $LOG
exec 2>> $LOG
exec < /dev/null

# можно только руту
_UID=$(id -u)
[ $_UID -ne 0 ] && { echo "You need to be root to do it."; exit 1; }

# для корректного завершения работы
PreTrap() { 
	QUIT=1 
}
CheckQuit() {
	if [ ! -z $QUIT ]
        then
               	echo "Interrupt recieved, stopping transcoding and exiting."
                exit 0
	fi
}
trap PreTrap SIGINT SIGTERM

# параметры ожидания
SLEEP=1
MAXSLEEP=300

# основной цикл демона
while [ true ]
do
	FILES=$INDIR*.mp4
	for F in $FILES
	do
		if [ -f "$F" ]
		# обрабатываем файлы если есть
		then 
			echo "Processing $F"
			BASENAME=$(basename $F .mp4)

			SD="$OUTDIR$BASENAME-sd.mp4"
			echo -n "SD: $SD..."
			$FFMPEG -i $F -vf scale=320:240 $SD > /dev/null 2>&1
			[ $? -eq 0 ] && echo "OK" || echo "ERROR"

			HD="$OUTDIR$BASENAME-hd.mp4"
			echo -n "HD: $HD..."
			$FFMPEG -i $F -vf scale=1280:720 $HD > /dev/null 2>&1
			[ $? -eq 0 ] && echo "OK" || echo "ERROR"
	
			POSTER="$OUTDIR$BASENAME.png"
        		echo -n "Poster: $POSTER..."
	        	$FFMPEG -i $F -ss 00:03 -vframes 1 -vf scale=640:-1 $POSTER > /dev/null 2>&1
			[ $? -eq 0 ] && echo "OK" || echo "ERROR"	
	
			rm -f $F
			SLEEP=1
			CheckQuit
		elif [ $SLEEP -lt $MAXSLEEP ]
		# увеличиваем интервал ожидания
		then
			((SLEEP++))
		fi
	done
	
	CheckQuit
	# чтоб можно было заершить процесс и не ждать пока проснется
	sleep $SLEEP & 
	wait
done

exit 0
