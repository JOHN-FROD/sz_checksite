#!/bin/bash

#如果传入的参数少于1个，则输出正确的格式，并返回255
if [ $# -lt 1 ]; then
	echo $0 "target [ time_out msg_file ]"
	exit 255
fi

#如果传入的参数等于3个，则把第三个参数赋值给out_file，否则就标准输出
if [ $# -eq 3 ]; then
	out_file=$3
else
	out_file=/dev/stdout
fi

#如果传入的参数大于1个，则把第二个参数赋值为超时时间，否则就默认为4
if [ $# -gt 1 ]; then
	TIMEOUT=$2
else
	TIMEOUT=4
fi

#尝试连接3次
cnt=1
while [ $cnt -le 3 ]; do
	let cnt++
	echo -n check http v6: $1 >> $out_file		##不换行输出“check http v6: 输出的第一个参数”
	x=$(curl -m $TIMEOUT -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.83 Safari/537.36" -6 -i $1 2>/dev/null | awk '/^HTTP/ {print $1}')
	#执行curl语句，正则匹配HTTP开头的行并返回第一个字段，有返回值就输出", get http response header $1"，并返回0；没有返回值则输出错误，并返回1
	if [ ! -z "$x" ]; then
		echo ", get http response header" $x >> $out_file
		exit 0
	else
		echo ", "failed to get http response header >> $out_file
	fi
done

exit 1
