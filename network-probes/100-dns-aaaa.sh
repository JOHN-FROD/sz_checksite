#!/bin/bash

#如果传入的参数少于1个，则输出正确的格式，并返回255
if [ $# -lt 1 ]; then
	echo $0 "target [ msg_file ]"
	exit 255
fi

#如果传入的参数等于2个，则把第三个参数赋值给out_file，否则就标准输出
if [ $# -eq 2 ]; then
	out_file=$2
else
	out_file=/dev/stdout
fi

#尝试解析3次
cnt=1
while [ $cnt -le 3 ]; do
	let cnt++
	echo -n check dns aaaa: $1 >> $out_file		#不换行输出“check dns aaaa: 输出的第一个参数”
	x=$(host -t aaaa $1 | awk '/has IPv6 address/ {print $5}')
	#执行host语句，正则匹配has IPv6 address的那一行，打印第5个字段，有返回值就输出", get http response header $1"，并返回0；没有返回值则输出错误，并返回1
	if [ ! -z "$x" ]; then
		echo ", ipv6 is" $x >> $out_file
		exit 0
	else
		echo " "has no dns aaaa >> $out_file
	fi
done

exit 1
