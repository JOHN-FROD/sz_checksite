<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>深圳高校教育网IPv6支持情况</title>
    <script src="https://cdn.bootcdn.net/ajax/libs/axios/0.21.1/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
</head>
<body>
<div id="wrapper">
    <div class="container-fluid">
        <div class="jumbotron jumbotron-fluid">
            <div class="container">
                <h2 class="display-6">深圳高校教育网HTTP、HTTPS、HTTP/2支持情况</h2>
<!--                <p class="lead"></p><form method="get" action="onlinecheck.php">请输入主机名：<input name="hostname" value="www.ustc.edu.cn"><input type="submit" name="cmd" value="开始测试">-->
            </div>
        </div>
        <div class="test-time">
            测试时间： <span>{{time}}</span>
        </div>
        <div class="card">
            <h5 class="card-title"></h5>
            <p class="card-text">
            <table border="1" cellspacing="0" id="myYable" class="display">
                <thead>
                    <th></th>
                    <th>单位</th>
                    <th>网站</th>
                    <th>v4 HTTP</th>
                    <th>v4 HTTPS</th>
                    <th>v4 HTTP2</th>
                    <th>v6解析</th>
                    <th>v6 HTTP</th>
                    <th>v6 HTTPS</th>
                    <th>v6 HTTP2</th>
                </thead>
                <tbody>
                    <tr class="item" v-for="(item,index) in result">
                        <td>{{ index+1 }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.hostname }}</td>
                        <td>{{ item.ipv4 }}</td>
                        <td>{{ item.httpsv4 }}</td>
                        <td>{{ item.http2v4 }}</td>
                        <td>{{ item.aaaa }}</td>
                        <td>{{ item.ipv6 }}</td>
                        <td>{{ item.httpsv6 }}</td>
                        <td>{{ item.http2v6 }}</td>
                    </tr>
                </tbody>
            </table>
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script>
    var app = new Vue({
        el:"#wrapper",
        data:{
            result:"",
            time:"",
        },
        mounted:function (){
            this.getResult();
        },
        methods:{
            getResult:function (){
                axios.get("./result.json.php")
                    .then((response)=>{
                        //console.log(response.data);
                        this.result = response.data.result;
                        this.time = response.data.endDatetime;
                    },function (err){
                        console.log(err);
                    });
            },
        }
    })
</script>
</body>
</html>