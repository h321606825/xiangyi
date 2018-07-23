# 学生端
----
## 1. 心愿墙
**返回参数**

- 心愿分类（根据筛选的标签）
- 心愿id
- 心愿内容
- 是否为管理员分配
- 发布时间
- 截止日期


```
GET/wish/list
return:
{
    "code": 0,
    "massage":"",
    "data":
    {
        "accepted":[
        {
            "id":"心愿id",
            "time":"发布时间，直接显示",
            "deadline":"截止日期，直接显示",
            "content":"内容，直接显示",
            "admined":"0不是，1是"
        }
        ],
        "unaccepted":[
        "id":"心愿id",
            "time":"发布时间，直接显示",
            "deadline":"截止日期，直接显示",
            "content":"内容，直接显示",
            "admined":"0不是，1是"
        ]
    }
}
```



### 心愿墙 图1-1
**返回参数**

- 心愿id
- 发布人
- 发布人联系方式

```
GET /wish/pubPage

return :
{
    "code": 0,
    "message": "",
    "data": {
        "id": "心愿id",
        "guy":"发布人，直接显示",
        "phone": "联系方式,直接显示"
    }
}
```


**请求参数**

- 心愿id
- 认领人
- 认领联系方式


```
GET /wish/pubPage
payload:
{
    "id":"心愿id",
    "angle_guy":"认领人姓名",
    "angel_phone":"联系方式"
}

```



## 2. 任务列表 
### 2.1 已完成心愿

**返回参数**

- 心愿id
- 心愿内容
- 是否被评价



```
GET /wish/stulistaccept

return:
{
    "code": 0,
    "message": "",
    "data": {
                "id": "心愿id",
                "content": "内容，直接显示",
                "quality":{
                    "0":"待评价",
                    其他：null
                }
}
```


### 任务列表 图2-1

**返回参数**

- 心愿id
- 认领人
- 联系方式
- 服务时长



```
POST /wish/stuuncomment


return :
{
    "code": 0,
    "message": "",
    "data": {
        "angel_name":"认领人姓名",
        "angel_phone":"联系方式"，
        "work_time":"志愿时长"
    }
}
```


### 任务列表 图2-2

**返回参数**

- 心愿id
- 认领人
- 联系方式
- 完成质量
- 志愿时长

```
GET/wish/stucomment
return:
{
    "id":"心愿id",
    "angle_id":"认领人，直接显示",
    "angle_phone": "联系方式,直接显示",
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

### 2.2 待完成心愿 
- 学生id
- 学生认领未完成的心愿内容

```
GET/wish/stulistunaccepted
return:
{
    "code": "0",
    "massage":"",
    "data":{
                "id": "心愿id",
                "content": "内容，直接显示"
    }
}
```

## 3. 个人列表

**返回参数**

- 学生姓名
- 学生学号
- 志愿总时长

```
GET /wish/pubpage
return:{
    "angel_guy":"学生姓名",
    "angel_id":"学生id",
    "work_time":"志愿总时长"
}
```

