# 管理端
## 1. 心愿列表
### 1.1 已分配心愿

**返回参数**

- 心愿id
- 心愿内容
- 心愿分配对象
- 心愿分配对象id

```
GET/wish/admassignedlist
return:
{
    "code":"0",
    "message":"",
    "data":{
        "id":"心愿id",
        "content":"心愿内容",
        "angel_guy":"心愿分配对象,直接显示"
    }
    
}
```

### 已分配心愿 图1-1

**请求参数**
 - 学生id

```
GET/wish/studentinfo
layload :
    "angel_id":"学生id"
```



**返回参数**

- 学生姓名
- 学生学号
- 志愿总时长


```
GET/wish/info
return :
{
    "angel_id":"学生姓名",
    "angel_guy":"学生学号",
    "work_time":"志愿服务时长"
}
```


### 已分配心愿 图1-2

**请求参数**

- 心愿id

**返回参数**

- 心愿内容
- 认领人
- 联系方式
- 志愿时长


```
GET/wish/info

"id":"心愿id"

return:
{
    "id":"心愿id",
    "content":"心愿内容，直接显示",
    "angel_id":"认领人，直接显示",
    "angel_phone": "联系方式,直接显示",
    "work_time":"志愿时长",
    "quality":[
    0:其他
    100：A
    80:B
    60:C
    40:D
    ]
}
```

### 1.2 待分配心愿

**返回参数**

- 心愿id
- 心愿内容


### 待分配心愿


```
GRT/wish/admunassignedlist

return:
{
    "id":"心愿id",
    "content":"心愿内容",
}
```

**请求参数**

- 认领人
- 联系方式



```
GRT/wish/admpage

return:
{
    "id":"心愿id",
    "guy":"联系人",
    "phone":"联系方式"
}


POST/wish/admpage
payload:
{
    "angel_name"："认领人姓名",
    "angel_phone":"认领人联系方式"
}
```

**返回参数**

- 分配是否成功


```
GET/wish/pub
return：
{
    "ture："成功",
    "false":"失败"
}
```
