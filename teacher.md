# 教师端
----
## 1. 心愿列表
### 1.1 已认领心愿
**返回参数**

- 心愿id
- 心愿内容
- 是否完成
- 完成时间


```
GET /wish/teaaccept
return :
{
    "code": 0,
    "message": "",
    "data":
    {
        "id": "心愿id",
        "done":
        {
            "0":"待完成",
            "1":"已完成"
        }
        "angel_phone": "联系方式,直接显示"
    }
}
```


### 已认领心愿 图1-1
**返回参数**

- 心愿id
- 认领人
- 认领人联系方式
- 完成质量
- 志愿时长


```
GET /wish/teadone
return :
{
    "code": 0,
    "message": "",
    "data": {
        "id": "心愿id",
        "angel_guy":"认领人，直接显示",
        "angel_phone": "联系方式,直接显示",
        "quality":{
            0:"未评价",
            40:"D",
            60:"C",
            80:"B",
            100:"A"
        },
        work_time:"志愿时长，直接显示"
    }
}
```


### 已认领心愿 图1-2
**返回参数**

- 心愿id
- 认领人
- 联系方式



```
POST/wish/teaundone
return:
{
    "code": 0,
    "message": "",
    "data": {
        "id": "心愿id",
        "angel_guy":"认领人，直接显示",
        "angel_phone": "联系方式,直接显示",
        "quality":"待完成"
    }
}
```


**请求参数**

- 心愿内容
- 教师联系人
- 教师联系方式


```
POST /wish/pub

payload :

{
    "content": "心愿内容",
    "guy": "联系人",
    "phone": "联系方式",
    "deadline": "截止时间"
}
return :
{
    "code": 0,
    "message": "",
    "data": null
}
```


**返回参数**

- 新建是否成功 

```

```


### 1.2 待认领心愿

**返回参数**

- 心愿id
- 心愿内容 
- 是否可以再次发送


```
GET/wish/teaunaccept
return
{
    "id":"心愿id",
    "content":"心愿内容",
    "resend"{
        1:"可以再次发送",
        0:"不可以再次发送"
    }
}
```

**请求参数**

- 再次发送的心愿id

```
POST /wish/repub

payload :

{
    "content": "心愿内容",
    "guy": "联系人",
    "phone": "联系方式",
    "deadline": "截止时间"
}
return :
{
    "code": 0,
    "message": "",
    "data": null
}
```


**返回参数**

- 是否再次发送成功


## 2. 心愿库

### 2.1 待评价心愿

**返回参数**

- 待评价心愿id
- 待评价心愿内容

```
GET/wish/uncomment
return
{
    "id":"心愿id",
    "content":"心愿内容，直接显示"

}
```

### 待评价心愿 图2-1（留坑）

**请求参数**

- 心愿id
- 心愿内容
- 联系人
- 联系人联系方式
- 认领人
- 认领人联系方式

```
GET/wish/teauncomment
payload:
"id": "心愿id"
return:
{
    "code":"0",
    "massage":"",
    "data":{
        "id":"心愿id",
        "content":"心愿内容",
        "guy":"联系人",
        "phone":"联系人联系方式",
        "angel_guy":"认领人",
        "angel_phone":"认领人联系方式"
    }
}
```


**留坑**

## 3. 个人列表

**返回参数**

- 姓名
- 工号
- 剩余时间

```
GET/wish/userinfo
return:
{
    "nickname":"姓名，直接显示",
    "phone":"工号，直接显示",
    "remaintime":"剩余时间，直接显示"
}
```



















