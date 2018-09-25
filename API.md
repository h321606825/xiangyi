# 1. 老干部帮扶系统-V2.0-API

<!-- TOC -->

- [0. tips](#0-tips)
- [1. 用户](#1-用户)
  - [1.1. 学生注册](#11-学生注册)
  - [1.2. 登录](#12-登录)
  - [1.3. 学生个人信息](#13学生个人信息)
  - [1.4. 教师个人信息](#14教师个人信息)
- [2. 心愿](#2-心愿)
  - [2.1. 教师列表](#21-教师列表)
  - [2.2. 发布心愿（发布）](#22-发布心愿（发布）)
  - [2.3. 获取心愿信息](#23-获取心愿信息)
  - [2.4. 取消心愿](#24-取消心愿)
  - [2.5. 接受心愿](#25-接受心愿)
  - [2.6. 评价完成心愿](#26-评价完成心愿)
  - [2.7. 学生心愿列表](#27-学生心愿列表)
  - [2.8. 管理心愿列表](#28-管理心愿列表)
  - [2.9. 学生确认](#29-学生确认)
- [3. 管理员](#3-管理员)
  - [3.1. 分配](#31-分配)
  - [3.2. 分配列表](#32-分配列表)
  - [3.3. 管理信息查询](#33-管理信息查询)
  - [3.4. 管理心愿文件导出](#34-管理心愿文件导出)
  - [3.5. 管理教师信息录入](#35-管理教师信息录入)
  - [3.6. 管理时长导出](#36-管理时长导出)


<!-- /TOC -->

# 0. tips

- 以下所有url的根均为 http://
- code :
  - 0 : 一切正常
  - 1 : 直接将 message 展示给用户
  - 2 : 验证用户无效,跳转至登录页
 - flag :
    - 0 : 求助学生
    - 1 ：求助老师
 - kind ：
   - 1 ： 购物类
   - 2 ： 取快递
   - 3 ： 校园出行
   - 4 ： 上门陪伴
   - 5 ： 整理资料
   - 6 ： 辅导手机应用
   - 7 ： 读报
 ```
 undone : 待完成,
 unaccepted : 待接受,
 unevaluate : 待评价,
 done : 已完成
 ```
---

# 1. 用户
## 1.1. 登录

- payload /user/login

  - post:
  ```
  {
      "accound":"账号",
      "password":"密码"
  }
  ```
  - return：
  ```json
  {
    "code": 0,
    "message": "",
    "data": {
          admin:{
          1:管理员
          2：老师
          3：学生
          }
    }
  }
  ```

---
## 1.2.  学生个人信息
 - GET /user/stu-info
   -  post:
 ```
 "account":"学号"
 ```
  - return:
 ```json
 {
     "code":0,
     "message":"",
     "data":[
     {
         "name":"姓名",
         "account":"学号",
         "phone":"联系方式",
         "sex":"性别",
         "college":"学院",
         "time":"总时长"
     }
     ]
 }
``` 
---

## 1.4.  教师个人信息
- GET /user/tea-info
  
  - return:
 ```json
 {
     "code":0,
     "message":"",
     "data":[
     {
         "name":"姓名",
         "account":"工号",
         "sex":"性别",
         "phone":"联系方式",
         "college":"学院"
     }
     ]
 }
 ```
---

# 2. 心愿

## 2.1. 教师列表

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
                "angel_phone":"学生联系方式",
                "college":"学生学院"
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
                "angel_phone":"学生联系方式",
                "college":"学生学院"
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
                "college":"学生学院"
        }
        ]
    }
}
```

---


---

## 2.2. 发布心愿（发布）

- POST /wish/pub

- payload :

```json
{
    "content": "心愿内容",
    "guy": "联系人",
    "phone": "联系方式",
    "deadline": "截止时间",
    "college":"学院",
    "flag" : {
        "1" : "求助老师",
        "0" ："求助学生"
    }
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

## 2.3. 获取心愿信息

- GET /wish/info?
  - id : 心愿id

- return :
    "done":"1已完成",
    "undone":"2未完成",
    "unevaluate":"3未评价",
    "unaccept":"4未接受"

```json
{
    "code": 0,
    "message": "",
    "data": {
        "id": "心愿id",
        "content": "心愿内容",
        "img": "图片url",
        "guy": "联系人",
        "college":"学院",
        "phone": "联系方式",
        "deadline": "截止日期",
        "angel": {
            "guy": "认领人",
            "phone": "联系方式",
            "done": "是否已完成",
            "college":"学院"
        }
    }
}
```

---

## 2.4. 取消心愿

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

## 2.5. 接受心愿

- POST /wish/accept

- payload :
```json
{
    "id": "心愿id",
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

## 2.6. 评价完成心愿

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
## 2.7. 学生心愿列表

- GET /wish/stulist

- return :
```
{
  undone:"未完成",
  unaccepted："未认领",
  unevaluate："未评价",
  done："已完成"
}
```

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
                "angel_id":"学生学号",
                "angel_guy"："学生姓名",
                "angel_phone":"学生电话",
                "college":"学生学院"
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
        "unevaluate":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "angel_id":"学生学号",
                "angel_guy"："学生姓名",
                "angel_phone":"学生电话",
                "angel_college":"学生学院"
        }
        ]
        "done":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "angel_id":"学生学号",
                "angel_guy"："学生姓名",
                "angel_phone":"学生电话",
                "angel_college":"学生学院"
        }
        ]
    }
}
```
---
## 2.8. 管理心愿列表

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
                "angel_id": "学生id",
                "angel_guy":"学生姓名",
                "angel_phone":"学生电话",
                "angel_college":"学生学院"
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
                "content": "内容，直接显示",
                "angel_id":"学生id",
                "angel_guy":"学生姓名",
                "angel_phone":"学生电话"
            }
        ]
        "unevaluate":[
        {
                "id": "心愿id",
                "time": "发布时间，直接显示",
                "deadline": "截止日期，直接显示",
                "content": "内容，直接显示",
                "angel_id":"学生id",
                "angel_guy":"学生姓名",
                "angel_phone":"学生电话"
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
 ## 2.9. 学生确认
 
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


# 3. 管理员
## 3.1. 分配
* POST/wish/admassign

* payload:

```
{
     "id":"心愿id",
     "angel_id":"学生学号"
     "angel_guy":"要分配的人员姓名",
     "angel_phone":"要分配的人员联系方式"
}
   
```

* return

```
{
    "true":"分配成功",
    "false":"分配失败"
}
```
## 3.2. 分配列表

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
 ## 3.3. 管理信息查询
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
---
## 3.4. 管理心愿文件导出
---
## 3.5. 管理教师信息录入
---
## 3.6. 管理时长导出
---
## 3.7. 修改密码
---
## 3.8. 管理学生信息导入
