# tools
===============
### 常用方法合集
> 更新完善中

> 搜集平时项目中常用的方法，备份日后使用
#### 安装
> composer require lzqqdy/tools
##### Demo
```
use lzqqdy\tools\Http;
Http::get($url);
```
```
* 多维数组按照某字段排序  sortArrByOneField  
* 多维数组格式化日期  formatDate 
* 遍历文件夹获取文件树  getFilesTree 
* 将数据集格式化成树形层次结构  toLayer 
* 二维数组根据键值去重  unique 
* 获取指定键所有值的数组  getCols 
* 将一个二维数组按照指定字段的值分组  groupBy 
* 从数组中删除空白的元 removeEmpty 
* 获取数组层数  getArrayLevel 
* curl请求Api接口  requestApi 
* 由经纬度算距离  getDistance 
* php将时间处理成“xx时间前”  formatTime 
* 二维数组根据多个字段排序 sortArrByManyField
* 获取用户IP地址 getIp
* 将字符串分割为数组 mb_str_split
* 生成不重复的随机数 get_rand_number
* 按符号截取字符串的指定部分 cut_str
* 获取二维数组中的某一列 get_arr_column
* 多维数组转化为一维数组 array_multi2single
* 获取服务器ip地址 serverIP
* 格式化字节 formatBytes
* 生成随机颜色 randomColor
* 将一维数组解析成键值相同的数组 parseArr
* 获取本周所有日期 get_week
* 获取最近七天所有日期 get_weeks
* 一维数组转二维 toMapping
* 判断二维数组是否存在某键值对 if_array
* 数组转xml arrayToXml
* 将二维数组以指定的key作为数组的键名 convert_arr_key
* 两个数组的笛卡尔积 combineArray
* 数组随机抽出一个 arrayRandOne
* 图片转Base64 imageToBase64
* Base64保存为图片 base64ToImage
* 生成UUID uuid
* 生成订单号 createOrderNum
* 生成随机密码 getRandPass
* 手机号码中间4位用星号替换显示 hideTel
* 计算两个时间戳之间相差的时间 timeDiff
* 替换富文本编辑器中的图片地址 replacePicUrl
* ......
```
### [Arr.php](https://github.com/lzqqdy/tools/blob/master/src/Arr.php)
> 数组处理
### [File.php](https://github.com/lzqqdy/tools/blob/master/src/File.php)
> 文件处理：文件遍历，文件&文件夹操作
### [Http.php](https://github.com/lzqqdy/tools/blob/master/src/Http.php) 
> HTTP请求（get/post）
### [Random.php](https://github.com/lzqqdy/tools/blob/master/src/Random.php)
> 随机生成
### [Str.php](https://github.com/lzqqdy/tools/blob/master/src/Str.php) 
> 字符串处理
### [Time.php](https://github.com/lzqqdy/tools/blob/master/src/Time.php) 
> 时间处理
### [Tree.php](https://github.com/lzqqdy/tools/blob/master/src/Tree.php) 
> 数据集处理
### [Other.php](https://github.com/lzqqdy/tools/blob/master/src/Other.php) 
> 其他方法
### [WeChat.php](https://github.com/lzqqdy/tools/blob/master/src/WeChat.php) 
> 小程序常用接口
### [Algorithm.php](https://github.com/lzqqdy/tools/blob/master/src/Algorithm.php)
> PHP实现基础算法
### [Validate.php](https://github.com/lzqqdy/tools/blob/master/src/Validate.php)
> 常用验证规则
### [Image.php](https://github.com/lzqqdy/tools/blob/master/src/Image.php)
> 图片处理

