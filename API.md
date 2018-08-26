# 1. 老干部帮扶系统-V2.0-API

<!-- TOC -->

- [0. tips](#0-tips)
- [1. 用户](#1-用户)
  - [1.1. 登录](#11-登录)
  - [1.2. 学生个人信息](#12-学生个人信息)
  - [1.3. 教师注册](#13-教师注册)
  - [1.4. 修改电话](#14-修改电话)
  - [1.5. 教师个人信息](#15-教师个人信息)
- [2. 心愿](#2-心愿)
  - [2.1. 教师心愿列表](#21-心愿列表)
  - [2.2. 发布心愿（获取页面数据）](#22-发布心愿（获取页面数据）)
  - [2.3. 发布心愿（发布）](#23-发布心愿（发布）)
  - [2.4. 获取心愿信息](#24-获取心愿信息)
  - [2.5. 取消心愿](#25-取消心愿)
  - [2.6. 接受心愿](#26-接受心愿)
  - [2.7. 评价完成心愿](#27-评价完成心愿)
  - [2.8. 学生心愿列表](#28-学生心愿列表)
  - [2.9. 管理心愿列表](#29-管理心愿列表)
  - [2.10. 学生确认](#210-学生确认)
  - [2.11. 教师确认](#211-教师确认)
- [3. 图片](#3-图片)
  - [3.1 图片上传](#31-图片上传)
- [4.管理员](#4-管理员)
  - [4.1. 分配](#41-分配)
  - [4.2. 分配列表](#42-分配列表)
  

<!-- /TOC -->

# 0. tips

- 以下所有url的根均为 http://wish.jblog.info/v1
- code :
  - 0 : 一切正常
  - 1 : 直接将 message 展示给用户
  - 2 : 验证用户无效,跳转至登录页

---

# 1. 用户

## 1.1. 登录

- POST /user/login
- payload :

```json
{
    "account": "账号",
    "password": "密码"
}
```

- return :

```json
{
    "code": 0,
    "message": "",
    "data": 
    {
        "admin":
        {
            "1": "管理员",
            "2": "老师",
            "3":"学生"
        }
        
    }
}
```
## 1.2. 学生个人信息

- GET /user/stu_info
- return :

```json
{
    "code": 0,
    "message": "",
    "data":[
    {
      "name":"学生姓名,直接显示",
      "acc":"学生学号",
      "phone":"学生联系方式",
      "time":"时长，直接显示"
    }
    ]
}
```

## 1.3.学生注册
 
 - POST /user/stu_register
 - payload:
 ```json
{
    "account": "账号",
    "nickname":"学生姓名",
    "phone": "电话号"
}
```

## 1.3. 教师注册

- POST /user/tea_register
- payload :

```json
{
    "account": "账号",
    "phone": "电话号"
}
```

## 1.4. 修改电话

- POST /user/alter
- payload：
```json
{
  "phone":"联系方式"
}
```
- return：
```json
{
    "code": 0,
    "message": ""
}
```

## 1.5. 教师个人信息

- GET /user/tea_info

- return：
```json
{
    "code": 0,
    "message": "",
    "data":[
    {
      "acc":"工号",
      "name":"姓名",
      "phone":"电话"
    }
    ]
}
```
---

# 2. 心愿

##  2.1.  教师心愿列表

- GET /wish/tealist

- return :

```json
{
    "code": 0,
    "message": "",
    "data": {
        "undone":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "angel_guy": "学生姓名",
                "angel_id":"学生学号",
                "angel_phone":"学生联系方式"
        }
        ]
        "unaccepted": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
            }
        ]
        "unevaluate":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "angel_guy": "学生姓名",
                "angel_id":"学生学号",
                "angel_phone":"学生联系方式"
        }
        ]
        "done":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "work_time":"志愿时长",
                "quality":"服务质量",
                "angel_guy": "学生姓名",
                "angel_id":"学生学号",
                "angel_phone":"学生联系方式"
        }
        ]
    }
}
```

---

## 2.2. 发布心愿（获取页面数据）

- GET /wish/pubPage

- return :

```json
{
    "code": 0,
    "message": "",
    "data": {
        "guy": "联系人",
        "phone": "联系方式"
    }
}
```

---

## 2.3. 发布心愿（发布）

- POST /wish/pub

- payload :

```json
{
    "content": "心愿内容",
    "img": "图片url，通过图片上传方法获取",
    "guy": "联系人",
    "phone": "联系方式",
    "deadline": "截止时间"
}
```

- return :

```json
{
    "code": 0,
    "message": "",
    "data": null
}
```

---

## 2.4. 获取心愿信息

- GET /wish/info?
  - id : 心愿id

- return :

"done":"已完成",
"undone":"未完成",
"accept":"接受",
"unaccept":"未接受"

```json
{
    "code": 0,
    "message": "",
    "data": {
            "id": "心愿id",
            "state":[
              "done":"1"
            ]
            "content": "心愿内容",
            "quality": "评价等级",
            "guy": "联系人",
            "phone": "联系方式",
            "deadline": "截止日期",
            "angel": {
                "guy": "认领人",
                "phone": "联系方式"
              }
    }
}
```

---

## 2.5. 取消心愿

- POST /wish/cancel

- payload :
```json
{
    "id": "心愿id",
    "reason": "取消原因"
}
```

- return :

```json
{
    "code": 0,
    "message": "",
    "data": null
}
```

---

## 2.6. 接受心愿

- POST /wish/accept

- payload :
```json
{
    "id": "心愿id",
    "guy": "联系人"
    "phone": "联系方式"
}
```

- return :

```json
{
    "code": 0,
    "message": "",
    "data": null
}
```

---

## 2.7. 评价完成心愿

- POST /wish/confirm

- payload :
```json
{
    "id": "心愿id",
    "time": "服务时长",
    "judge": "评价(A,B,C,D)"
}
```

- return :

```json
{
    "code": 0,
    "message": "",
    "data": null
}
```
---
## 2.8. 学生心愿列表

- GET /wish/stulistall

- return :
{
  undone:"未完成"，
  unaccepted：‘未认领’
  unconfirm：“未确认”
  unevaluate：“未评价”
  done：“已完成”
  
  
}

```json
{
    "code": 0,
    "message": "",
    "data": {
        "undone":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "quality":"完成质量,直接显示",
                "work_time":"志愿时长，直接显示"
        }
        ]
        "unaccepted": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "quality":"完成质量,直接显示",
                "work_time":"志愿时长，直接显示"
            }
        ],
        "unconfirm": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
            }
        ]
        "unevaluate":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
        }
        ]
        "done":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
        }
        ]
    }
}
```
---

## 2.9. 管理心愿列表

 - GET /wish/admlist
 - return:
 
 
 ```json
{
    "code": 0,
    "message": "",
    "data": {
        "undone":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "quality":"完成质量,直接显示",
                "work_time":"志愿时长，直接显示"
        }
        ]
        "unaccepted": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
            }
        ],
        "unconfirm": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
            }
        ]
        "unevaluate":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
        }
        ]
        "done":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content" : "内容，直接显示",
                "angel_guy" : "学生姓名",
                "angel_phone" : "学生电话",
                "angel_id" : "学生学号"
        }
        ]
    }
}
```
---
 ## 2.10. 学生确认
 
 - GET /wish/stuconfirm?
   - "id":"心愿id"
 - return:
```json
  {
    "code":"0",
    "message":"",
    "data":null
  }
```

---
## 2.11. 教师确认

 - GET /wish/teaconfirm?
 
   - "id":"心愿id"
 
 - return:
```json
  {
    "code":"0",
    "message":"",
    "data":null
  }
```
---

# 3. 图片

## 3.1. 图片上传

- POST /img/upload

- return :

```json
    "code": 0,
    "message": "",
    "data": "图片url"
```


# 4. 管理员

## 4.1. 分配

- POST/wish/admassign

- payload:

```
{
     "id":"心愿id",
     "angel_id":"学号",
     "angel_guy":"要分配的人员姓名",
     "angel_phone":"要分配的人员联系方式"
}
   
```

- return

```

{
    "code":0,
    "message":"",
    "data":null
}
```
## 4.2. 分配列表

- GET /wish/assignlist

- return :

```json
{
    "code": 0,
    "message": "",
    "data": {
        "assigned": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "quality":"完成质量,直接显示",
                "work_time":"志愿时长，直接显示"
            }
        ],
        "unasssigned": [
            {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示"
            }
        ]
        "cancel" : [
        {
                "id": "心愿id",
                "cancel_time": "发布时间，直接显示",
                "cancel_reason": "取消原因，直接显示",
                "content": "内容，直接显示"
        }
        ]
    }
}
```
 --- 
 ## 4.3. 管理信息查询
 - POST /wish/admchearch
  - payload:
  ```json
    {
      "angel_id":"学生学号"
    }
  ```
 - return:
 ```json
  {
    "angel_name":"学生姓名",
    "angel_phone":"学生电话",
    "work_time":"工作时长"
  }
 ```

