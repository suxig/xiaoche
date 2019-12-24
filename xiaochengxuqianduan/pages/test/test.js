var markersData = [];
var markersd = [];
const app = getApp();
var id = 0;
var amapFile = require('../../utils/amap-wx.js');
Page({
  array: [{ name: '单拍', price: '198' }, { name: '亲子套餐', price: '398' }, { name: '活动套餐', price: '598' }, { name: '女王套餐', price: '1198' }],
 // id: 0,             //进入页面时，默认选择第0个，如果不需要默认选中，注释掉就可以了 
  onLoad: function (options) {
    var that = this;
  },
  choseTxtColor: function (e) {
    var id = e.currentTarget.dataset.id;  //获取自定义的ID值  
    this.setData({
      id: id
    })
  },
});