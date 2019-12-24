const app = getApp();
var amapFile = require('../../utils/amap-wx.js');
var QQMapWX = require('../../utils/qqmap-wx-jssdk');
var markersData = [];
var markersd = [];
var aim1 = '';
var aim2 = '';
Page({
  data: {
    polyline:[],
    /*
    polyline: [{
      points: [{
        longitude: '112.922076',
        latitude: '27.89692796'
      }, {
        longitude: '112.9211319',
        latitude: '27.89697536'
      }, {
        longitude: '112.9211298',
        latitude: '27.89737109'
      }],
      color: "#FF0000DD",
      width: 3,
      dottedLine: true
    }],
    */
    scale: 13,
    latitude: "",
    longitude: "", 
    markers: [],
    textData: {},
      items1: [
        { name: 'nt1', value: '南图' },
        { name: 'nd', value: '南堕' },
        { name: 'gsyh', value: '工商银行' },
        { name: 'bm', value: '北门' },
        { name: 'bt', value: '北田' },
        { name: 'qq', value: '七区' },
      ],
      items2: [
        { name: 'nt2', value: '南图' },
        { name: 'sj', value: '四教' },
        { name: 'tml', value: '土木楼' },
        { name: 'bq18', value: '八区18栋' },
        { name: 'bqst', value: '八区食堂' },
      ],
},
  radioChange1: function (e) {
    aim1=e.detail.value
  },
  radioChange2: function (e) {
    aim2 = e.detail.value
  },
  onLoad: function () {
    var _this = this;
    var that = this;
    var myAmapFun = new amapFile.AMapWX({ key: 'a100014609a2c1c41bdc1f53550f3fd9' });
    wx.login({
      success: function (res) {
        if (res.code) {
          var data = {
            code: "1",
            longitude: "2",
            latitude: "3"
          }
          data.code = res.code
          try {
            wx.setStorageSync('key', data)
          } catch (e) {
          }
        }
      }
    })
    myAmapFun.getPoiAround({
      success: function (data) {
        markersData = data.markers;
        that.setData({
          latitude: markersData[0].latitude,
          longitude: markersData[0].longitude
        });
        try {
          var value = wx.getStorageSync('key');
          wx.getLocation({
            type: 'gcj02', //返回可以用于wx.openLocation的经纬度
            success: function (res) {
              value.latitude = res.latitude
              value.longitude = res.longitude
            }
          })
        } catch (e) {
          // Do something when catch error
        }
        wx.login({
          success: function (res) {
            if (res.code) {
              //发起网络请求
              wx.request({
                url: 'http://www.senlear.com/xiaochengxu/test.php',
                header: {
                  'content-type': 'application/x-www-form-urlencoded' // 默认值
                },
                method: "POST",
                data: {
                  code: value.code,
                  longitude: value.longitude,
                  latitude: value.latitude
                },
                success: function (res) {
                  getApp().globalData.passenger = res.data.passenger;
                  getApp().globalData.openid = res.data.openid;
                  /*_this.setData({
                    passenger: app.globalData.passenger,
                    openid: app.globalData.openid
                  })*/
                }
              })
              var packJson = res.data
              var markers_new = new Array()
              /*
              for (var p in packJson) {//遍历json数组时，这么写p为索引，0,1
                markers_new.push({
                  latitude: packJson[p].lat,
                  longitude: packJson[p].lon,
                  iconPath: "../../images/bullet.png",
                })
              }
              */
              markers_new.push({
                latitude: 112.9220711,
                longitude: 27.89692735,
                iconPath: "../../images/bullet.png",
              })
              that.setData({
                //openid: res.data,
                markers: markers_new,
              })
            } else {
              console.log('获取用户登录态失败！' + res.errMsg)
            }
          }

        })
/*
        wx.request({
          url: 'http://www.senlear.com/xiaochengxu/driver.php',
          header: {
            'content-type': 'application/x-www-form-urlencoded' // 默认值
          },
          method: "POST",
          data: {
            longitude: app.globalData.jing,
            latitude: app.globalData.wei,
            openid: app.globalData.openid
          },
          success: function (res) {
            var packJson = res.data
            var markers_new = new Array()
            for (var p in packJson) {//遍历json数组时，这么写p为索引，0,1
              markers_new.push({
                latitude: packJson[p].lat,
                longitude: packJson[p].lon,
                iconPath: "../../images/bullet.png",
              })
            }
            that.setData({
              //openid: res.data,
              markers: markers_new,
            })
          }
        })
*/

/*

        wx.request({
          url: 'http://www.senlear.com/xiaochengxu/driver.php',
          header: {
            'content-type': 'application/x-www-form-urlencoded' // 默认值
          },
          method: "POST",
          data: {
            longitude: app.globalData.jing,
            latitude: app.globalData.wei,
            openid: app.globalData.openid
          },
          success: function (res) {
            var packJson = res.data
            var polyline_new = new Array()
            var polyline_newp = new Array()
            var demo = new QQMapWX({
              key: 'VR3BZ-UUACF-FFUJ3-NSVNQ-VWMZQ-VQFD4' // 必填
            })
            
            for (var p in packJson) {//遍历json数组时，这么写p为索引，0,1
              polyline_newp.push({
                latitude: packJson[p].lat,
                longitude: packJson[p].lon,
                //iconPath: "../../images/bullet.png",
              })
            }
            
          
            for (var p in packJson) {//遍历json数组时，这么写p为索引，0,1
              demo.reverseGeocoder({
                location: {
                  latitude: packJson[p].wei,
                  longitude: packJson[p].jing
                },
                coord_type: 1,//经纬度类型
                success: function (res) {
                  var location = res.result.ad_info.location;
                  getApp().globalData.lat = location.lat;
                  getApp().globalData.lng = location.lng;
                }
              });
              polyline_newp.push({
                latitude: app.globalData.lat,
                longitude: app.globalData.lng,
                //iconPath: "../../images/bullet.png",
              });
              that.setData({
                long: app.globalData.lat,
                lai: app.globalData.lng,
                openid: 3
              })
            }
            
            
            polyline_new.push({
              points: polyline_newp,
              color: "#FF0000DD",
              width: 3,
              dottedLine: true
            })
            that.setData({
              //openid: res.data,
              polyline: polyline_new,
              //openid: "1"
            })
          }
        })

*/
      },
      fail: function (info) {
        wx.showModal({ title: info.errMsg })
      }
    })
      var interval = setInterval(function () {
        if (app.globalData.passenger == 1) {
          clearInterval(interval);
        }
        if(app.globalData.passenger ==1){          
        myAmapFun.getPoiAround({
          success: function (data) {
            wx.getLocation({
              type: 'gcj02', //返回可以用于wx.openLocation的经纬度
              success: function (res) {
                getApp().globalData.jing = res.longitude
                getApp().globalData.wei = res.latitude
              }
            })
            wx.request({
              url: 'http://www.senlear.com/xiaochengxu/driver.php',
              header: {
                'content-type': 'application/x-www-form-urlencoded' // 默认值
              },
              method: "POST",
              data: {
                longitude: app.globalData.jing,
                latitude: app.globalData.wei,
                openid: app.globalData.openid
              },
              success: function (res) {
                var packJson = res.data
                var markers_new=new Array()
                for (var p in packJson) {//遍历json数组时，这么写p为索引，0,1
                  markers_new.push({
                    latitude: packJson[p].wei-0.00042,
                    longitude: packJson[p].jing,
                    iconPath: "../../images/bullet.png",
                  })
                }
                that.setData({
                  //openid: res.data,
                  //markers: markers_new,
                })
              }
            })
          }
        })
      }
      }, 5000)
      var interval1 = setInterval(function () {
        if (app.globalData.passenger == 0) {
          clearInterval(interval1);
        }
        if (app.globalData.passenger == 1) {
          var longitude=0;
          var latitude =0;
          var a='';
          if (aim1.length == 0) a = aim2
          else a = aim1;
          if((aim1.length!=0)||(aim2.length!=0)){
            wx.getLocation({
              type: 'gcj02', //返回可以用于wx.openLocation的经纬度
              success: function (res) {
                getApp().globalData.jing = res.longitude,
                getApp().globalData.wei = res.latitude,
                that.setData({
                  lai: res.latitude,
                  long: res.longitude
                });
              }
            })
          wx.request({
            url: 'http://www.senlear.com/xiaochengxu/getd.php',
            header: {
              'content-type': 'application/x-www-form-urlencoded' // 默认值
            },
            method: "POST",
            data: {
              longitude: app.globalData.jing,
              latitude: app.globalData.wei,
              aim: a,
              openid: app.globalData.openid
            },
            success: function (res) {
              //_this.data.markers.longitude = res.data.jing;
              //_this.data.markers.latitude = res.data.wei;
              markersd = [{
                latitude: res.data.wei,
                longitude: res.data.jing,
                iconPath: "../../images/ic_position.png",
              }];
              that.setData({
                //openid: res.data.openid,
                long: res.data.wei,
                lai: res.data.jing,
                markers: markersd
              })
            }
          })
        }
        }
      }, 5000)
  },
})