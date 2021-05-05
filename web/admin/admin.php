<?php
include('../db.php');
$session = $_COOKIE['session'];
$q = "select uname from user where uname=? ";
$stmt = $mysqli->prepare($q);
$stmt->bind_param("s", $session);
$stmt->execute();
if ($stmt->fetch()){
}else {
    header("location: login.html");
    die("您无权访问,请先登陆");
}
?>
<!DOCTYPE html>
<html lang="zh-CN" >

<head>
    <meta charset="utf-8">
    <title>后台管理页面</title>
</head>

<body>
<div id="wrapper">
    <el-container>
        <el-header>
            <div class="container">
                <h1 class="display-6">后台管理页面</h1>
                <!--                <p class="lead"></p><form method="get" action="onlinecheck.php">请输入主机名：<input name="hostname" value="www.ustc.edu.cn"><input type="submit" name="cmd" value="开始测试">-->
            </div>
        </el-header>
        <el-container>
            <el-main>
                <el-table
                    :data="result.filter(data => !search || data.name.toLowerCase().includes(search.toLowerCase()))"
                    :default-sort = "{prop: 'score', order: 'descending'}"
                    border
                    stripe
                    @selection-change="handleSelectionChange"
                    style="width: 100%">
                    <el-table-column>
                        <template slot="header" slot-scope="scope">
                            <el-input
                                v-model="search"
                                size="medium"
                                prefix-icon="el-icon-search"
                                placeholder="请输入学校名字搜索，如：深圳大学">
                            </el-input>
                        </template>
                        <el-table-column
                            type="selection"
                            width="40">
                        </el-table-column>
                        <el-table-column
                            type="index"
                            width="40">
                        </el-table-column>
                        <el-table-column type="expand" width="50" label="详情">
                            <template slot-scope="props">
                                <el-form label-position="left" inline class="demo-table-expand">
                                    <h3>协议支持情况</h3>
<!--                                    <el-form-item label="v4 HTTP">-->
                                        <span>v4 HTTP: {{ props.row.ipv4 }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="v4 HTTPS">-->
                                        <span>v4 HTTPS: {{ props.row.httpsv4 }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="v4 HTTP2">-->
                                        <span>v4 HTTP2: {{ props.row.http2v4 }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="v6解析">-->
                                        <span>aaaa: {{ props.row.aaaa }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="v6 HTTP">-->
                                        <span>v6 HTTP: {{ props.row.ipv6 }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="v6 HTTPS">-->
                                        <span>v6 HTTPS: {{ props.row.httpsv6 }};</span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="v6 HTTP2">-->
                                        <span>v6 HTTP2: {{ props.row.http2v6 }}; </span>
<!--                                    </el-form-item>-->
                                    <h3>IPv6网络质量</h3>
<!--                                    <el-form-item label="丢包率">-->
                                        <span>丢包率: {{ props.row.loss }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="最短响应时间">-->
                                        <span>最短响应时间: {{ props.row.min }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="平均响应时间">-->
                                        <span>平均响应时间: {{ props.row.avg }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="最长响应时间">-->
                                        <span>最长响应时间: {{ props.row.max }}; </span>
<!--                                    </el-form-item>-->
                                    <h3>是否存在被黑风险</h3>
<!--                                    <el-form-item label="是否被篡改">-->
                                        <span>是否被篡改: {{ props.row.hacked  }}; </span>
<!--                                    </el-form-item>-->
<!--                                    <el-form-item label="篡改的敏感词">-->
                                        <span>篡改的敏感词: {{ props.row.keyword }}; </span>
<!--                                    </el-form-item>-->
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column
                            prop="name"
                            label="单位">
                        </el-table-column>
                        <el-table-column
                            prop="hostname"
                            label="网站">
                        </el-table-column>
                        <el-table-column
                            align="right">
                            <template slot="header" slot-scope="scope">
                                <el-button type="primary" size="medium" @click="dialogAddVisible = true" icon="el-icon-plus">添加学校</el-button>
                                <el-dialog title="添加学校和域名" :visible.sync="dialogAddVisible" align="left">
                                    <el-form :model="add">
                                        <el-form-item label="学校名称" :rules="{ required: true, message: '校名不能为空'}">
                                            <el-input v-model="add.name" auto-complete="off" ></el-input>
                                        </el-form-item>
                                        <el-form-item label="域名" :rules="{ required: true, message: '域名不能为空'}">
                                            <el-input v-model="add.hostname" auto-complete="off"></el-input>
                                        </el-form-item>
                                    </el-form>
                                    <div slot="footer" class="dialog-footer">
                                        <el-button @click="dialogAddVisible = false">取 消</el-button>
                                        <el-button type="primary" @click="onAdd" >添 加</el-button>
                                    </div>
                                </el-dialog>
                                <el-button size="medium" type="danger" icon="el-icon-delete" @click="onBatchDelete" :disabled="multipleSelection.length === 0">批量删除</el-button>
                            </template>
                            <template slot-scope="scope">
                                <el-button type="text" @click="onEdit(scope.$index, scope.row)" size="medium" icon="el-icon-edit">编辑</el-button>
                                <el-dialog title="编辑学校和域名" :visible.sync="dialogEditVisible" align="left">
                                    <el-form :model="editForm">
                                        <el-form-item label="学校名称" :rules="{ required: true, message: '校名不能为空'}">
                                            <el-input v-model="editForm.name" auto-complete="off"></el-input>
                                        </el-form-item>
                                        <el-form-item label="域名" :rules="{ required: true, message: '域名不能为空'}">
                                            <el-input v-model="editForm.hostname" auto-complete="off" ></el-input>
                                        </el-form-item>
                                    </el-form>
                                    <div slot="footer" class="dialog-footer">
                                        <el-button @click="cancel">取 消</el-button>
                                        <el-button type="primary" @click="onUpdate" >确 定</el-button>
                                    </div>
                                </el-dialog>
                                <el-button type="text" size="medium" @click="onDelete(scope.$index, scope.row)" icon="el-icon-delete">删除</el-button>
                            </template>
                        </el-table-column>
                    </el-table-column>
                </el-table>
                <p>共<span>{{result.length}}</span>条数据&nbsp;&nbsp;&nbsp;&nbsp;测试时间 <span>{{time}}</span></p>
            </el-main>
        </el-container>
    </el-container>
</div>

<script src="../js/http_cdn.bootcdn.net_ajax_libs_axios_0.21.1_axios.js"></script>
<script src="../js/http_cdn.jsdelivr.net_npm_vue_dist_vue.js"></script>
<!-- 引入组件库 -->
<script src="../js/http_unpkg.com_element-ui_lib_index.js"></script>
<!-- 引入样式 -->
<link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">

<script>
    var app = new Vue({
        el:"#wrapper",
        data:{
            result:[],
            time:"",
            search:"",
            oldhostname:"",
            dialogEditVisible: false,
            dialogAddVisible: false,
            dialogBatchDeleteVisible: false,
            currentIndex: "",
            multipleSelection: [],
            add:{
                name: '',
                hostname:'',
            },
            editForm:{},
        },
        mounted:function (){
            this.getResult();
        },
        methods:{
            getResult:function (){
                axios.get("../result.json.php")
                    .then((response)=>{
                        //console.log(response.data);
                        this.result = response.data.result;
                        this.time = response.data.endDatetime;
                    },function (err){
                        console.log(err);
                    });
            },
            onEdit:function (index, row){
                // 将数据的index传递过来用于实现数据的回显
                //console.log(this.result[index]);
                this.currentIndex = index;
                this.oldhostname = this.result[this.currentIndex].hostname;
                //console.log(this.oldhostname);
                this.editForm = Object.assign({}, row);
                // 设置对话框的可见
                this.dialogEditVisible = true;
            },
            onAdd:function () {
                //console.log(this.add);
                this.$confirm("是否执行添加?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning"
                })
                    .then(() => {
                        this.result.push(this.add);
                        this.dialogAddVisible = false;
                        let data = new FormData()
                        data.append('name', this.add.name)
                        data.append('hostname', this.add.hostname)
                        axios.post('./add.php', data).then(res => {
                            //console.log(res)
                            if(res.data === 'success'){
                                this.$message({
                                    type: "success",
                                    message: "添加成功!"
                                });
                            }else {
                                this.$message({
                                    type: "error",
                                    message: "添加失败! " + res.data
                                });
                            }
                        })
                        this.add = {};
                    })
                    .catch(() => {
                        this.$message({
                            type: "info",
                            message: "已取消添加"
                        });
                    });
            },
            onBatchDelete:function (){
                //console.log(this.multipleSelection);
                // 设置类似于console类型的功能
                this.$confirm("永久删除这些学校, 是否继续?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning"
                })
                    .then(() => {
                        // 移除对应索引位置的数据，可以对row进行设置向后台请求删除数据
                        for (let i = 0; i < this.result.length; i++) {
                            const element = this.result[i];
                            element.id = i
                        }
                        this.multipleSelection.forEach(element => {
                            this.result.forEach((e, i) => {
                                if (element.id === e.id) {
                                    this.result.splice(i, 1)
                                }
                            });
                        });
                        //后端
                        const length = this.multipleSelection.length;
                        for(let i = 0; i < length; i++) {
                            let data = new FormData()
                            data.append('hostname', this.multipleSelection[i].hostname)
                            axios.post('./delete.php', data).then(res => {
                                console.log(res.data)
                            })
                        }
                        this.dialogBatchDeleteVisible = false;
                        this.$message({
                            type: "success",
                            message: "删除成功!"
                        });
                    })
                    .catch(() => {
                        this.dialogBatchDeleteVisible = false;
                        this.$message({
                            type: "info",
                            message: "已取消删除"
                        });
                    });
            },
            onDelete:function (index, row) {
                // 设置类似于console类型的功能
                this.$confirm("永久删除该学校, 是否继续?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning"
                })
                    .then(() => {
                        // 移除对应索引位置的数据，可以对row进行设置向后台请求删除数据
                        this.result.splice(index, 1);
                        var data = new FormData()
                        data.append('hostname', row.hostname)
                        axios.post('./delete.php', data).then(res => {
                            console.log(res.data)
                        })
                        this.$message({
                            type: "success",
                            message: "删除成功!"
                        });
                    })
                    .catch(() => {
                        this.$message({
                            type: "info",
                            message: "已取消删除"
                        });
                    });
            },
            onUpdate:function () {
                this.$confirm("是否执行编辑?", "提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning"
                })
                    .then(() => {
                        // 移除对应索引位置的数据，可以对row进行设置向后台请求删除数据
                        this.$set(this.result,this.currentIndex,this.editForm);
                        this.dialogEditVisible = false;
                        var data = new FormData()
                        data.append('oldhostname', this.oldhostname)
                        data.append('hostname', this.editForm.hostname)
                        data.append('name', this.editForm.name)
                        axios.post('./edit.php', data).then(res => {
                            console.log(res.data)
                            if(res.data === 'success'){
                                this.$message({
                                    type: "success",
                                    message: "编辑成功!"
                                });
                            }else {
                                this.$message({
                                    type: "error",
                                    message: "编辑失败! " + res.data
                                });
                            }
                        })
                        // this.$message({
                        //     type: "success",
                        //     message: "编辑成功!"
                        // });
                    })
                    .catch(() => {
                        this.$message({
                            type: "info",
                            message: "已取消编辑"
                        });
                    });
            },
            cancel:function (){
                this.dialogEditVisible = false;
            },
            handleSelectionChange:function(val){
                //console.log(val);
                this.multipleSelection = val;
            }
        }
    })
</script>
</body>
</html>
