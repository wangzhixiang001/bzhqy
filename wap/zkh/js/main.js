var base_url =window.location.protocol+"//"+document.domain+"/bzhqy/index.php/performance/Front/";

function postAjax(url,param,func){
    $.post(base_url+url,param,func);
}

var vm = new Vue({
    el: '#app',
    template: "#temp",
    data: {
        layer: false, //公共弹层
        loading: false, //提交加载层
        layerText: "", //公共弹层提示
        layershare: false,
        layerAccount: false, //账号弹层
        departmentList: [], //部门列表
        groupList: [], //部门列表
        groupPeopleList: [], //部门列表
        PeopleList: [], //部门列表
        Ability: "", //专业能力
        Professional: "", //职业素养
        Notinfor: "", //备注信息
        selectText: "请选择部门",
        selectVal: "",
        selectID:'',
        popupDepartmenty: false,
        slots: [{
            flex: 1,
            values: [],
            className: 'slot1',
            textAlign: 'center'
        }],
        selectList: [], //部门账号 选择部门
        Account: "" ,// 01 账号
        recodes:[], // 已操作
        userinfo:null,
        options:[],
        hashid:'',
        inttx:0,
    },
    mounted() {
        window.addEventListener('scroll', this.windowScroll)
        this.onload();
       // this.department();
    },

    methods: {
        windowScroll: function() {
            //滚动条距离页面顶部的距离
            this.scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop //原生兼容
            console.log(this.scrollTop)
        },
        onload: function() {
            var _this = this;
            postAjax('init',"",function(data){
                data = eval('('+data+')');
                if(data.code==0){
                    _this.departmentList = data.data.data;
                    _this.recodes = data.data.recodes;
                    _this.options =data.data.options;
                    _this.hashfun("contId03");
                }else if(data.code ==400){
                    _this.selectList = data.data.departs;
                    _this.slots[0].values = data.data.departs;
                    _this.Account =localStorage.getItem("login_account_z");
                    if(_this.Account){
                        _this.LoginBtn();
                    }else {
                        _this.hashfun("contId01");
                    }
                }
            })
        },
        // 01 登录
        LoginBtn: function() {
            console.log(this.Account.length);
            if (this.Account.length <= 0) {
                this.$messagebox({
                    title: '温馨提示',
                    message: '请输入账号!'
                });
                return;
            }
            this.loading = true;
            var _this = this;
            postAjax('login',{number:this.Account},function(data){
                _this.loading = false;
                data =eval("("+data+")");
                if(data.code==0){
                    _this.onload();
                    if(data.data.staff ==1){
                        _this.$messagebox({
                            title: '温馨提示',
                            message:"你已被选中为员工代表，请为与你有上下游工作关系的部门主管进行审评",
                        });
                    }
                }else{
                    _this.$messagebox({
                        title: '温馨提示',
                        message:data.msg
                    });
                }
            })
        },

        // 点击选择账号
        selectAccount: function() {

            this.enterBtn();

        },
        // 02  选择部门
        select: function() {
            var _this = this;
            $.ajax({
                type: 'GET',
                data: {},
                url: "./json/login.json",
                dataType: 'json',
                success: function(data) {

                    if (data.code == 0) {
                        _this.selectList = data.list;
                        _this.slots[0].values = data.list

                    }
                    console.log(_this.selectList)
                }
            });
        },
        // 提交
        enterBtn: function() {
            var _this = this;
            _this.loading = true;
            postAjax('claim2',{
                departid:_this.selectID,
                role:1,
            },function(data){
                data = eval("("+data+")");
                _this.loading=false;
                if(data.code == 0){
                    _this.Account=data.data.number;
                    localStorage.setItem("login_account_z", _this.Account);
                    _this.layerAccount = true
                }else{
                    _this.$messagebox({
                        title: '温馨提示',
                        message: data.msg
                    });
                }

            })

        },
        enterBtn01: function() {
            this.layerAccount = false
            this.LoginBtn();
        },
        onValuesChangeDepartmenty(picker, values) {

            console.log(values[0])
            if (values[0] != undefined) {
                this.selectVal = values[0].department;
                this.selectID = values[0].id;
            }
        },

        sureDepartmenty: function() {
            this.popupDepartmenty = false;
            this.selectText = this.selectVal
        },
        cancelDepartmenty: function() {
            this.popupDepartmenty = false
        },
        // 获取部门列表
        department: function() {

            var _this = this;
            $.ajax({
                type: 'GET',
                data: {

                },
                url: "./json/departmentList.json",
                //  url:"http://psbc.bzh001.com/yylm/json/list.json",
                dataType: 'json',
                success: function(data) {
                    console.log(data.list)
                    if (data.code == 0) {
                        _this.departmentList = data.list;
                    }

                }
            });

        },

        // 点击部门
        departmenTap: function(index) {
            this.hashfun("contId04");
            console.log(index);
            if(this.departmentList[index].Personnel) {
                this.groupPeopleList = this.departmentList[index].Personnel;
            }else{
                this.groupPeopleList=[];
            }
            if(this.departmentList[index].group) {
                this.groupList = this.departmentList[index].group;
            }else{
                this.groupList=[];
            }
        },
        // 点击组
        groupTap: function(index) {
            this.hashfun("contId05");
            if(this.groupList[index].Personnel) {
                this.PeopleList = this.groupList[index].Personnel;
            }else{
                this.PeopleList=[];
            }
        },
        scoreTap: function(user) {
            this.userinfo =user;
            this.Professional='';
            this.Ability='';
            this.Notinfor="";
            this.hashfun("contId06");
        },
        // 提交
        submitBtn: function() {
            if (parseInt(this.Ability) < 0 || parseInt(this.Ability) > 50) {
                console.log(this.Ability)
                this.$messagebox({
                    title: '温馨提示',
                    message: '请填写0~50之间的数字!'
                });
                return;
            }
            if (parseInt(this.Professional) < 0 || parseInt(this.Professional) > 50) {
                this.$messagebox({
                    title: '温馨提示',
                    message: '请填写0~50之间的数字!'
                });
                return;
            }
            if (this.Ability == "" && this.Professional == "") {
                this.$messagebox({
                    title: '温馨提示',
                    message: '评分至少评一项',
                    // showCancelButton: true
                });
                return;
            }
            if (this.Notinfor.length <= 4) {
                this.$messagebox({
                    title: '温馨提示',
                    message: '原因及意见至少5个字符'
                });
                return;
            }
            this.layer = true;

        },
        SureBtn: function() {
            var _this = this;
            _this.layer = false;
            _this.loading = true;
            var options=[];

            if(this.Ability.length>0){
                options.push({
                    option:'专业能力成果',
                    score:this.Ability,
                });
            }
            if(this.Professional.length>0){
                options.push({
                    option:'职业素养表现',
                    score:this.Professional,
                });
            }
            postAjax('subAddAjax',{
                'options':options,
                'remark':_this.Notinfor,
                'userid':_this.userinfo.userid,
            },function(data){
                _this.loading = false;
                data=eval('('+data+')');
                if(data.code==0){
                    _this.recodes.push(_this.userinfo.userid);
                    history.go(-1);
                }else{
                    _this.$messagebox({
                        title: '温馨提示',
                        message: data.msg
                    });
                }
            })

        },

        blur: function() {
            console.log("失去焦点");
            $("body").scrollTop(this.scrollTop);
        },
        hashfun: function(hashid) {
            $(".main").hide()
            location.hash = hashid;
            $('#' + hashid + '').show();
        },

        state:function(userid){
            var state =false;
            $(this.recodes).each(function(i,e){
                   if( userid == e){
                       state= true;
                       return false;
                   }
            });
            return state;
        },
    },



    　watch: {
        Ability: function(curVal, oldVal) {

            if (!curVal) {
                return ;
            }

            curVal = curVal.replace(/[^\d]+/g, '') //专业能力成果
            if (!curVal) {
                this.Ability = oldVal;
            }
            if (parseInt(this.Ability) < 0 || parseInt(this.Ability) > 50) {
                console.log(this.Ability)
                this.$messagebox({
                    title: '温馨提示',
                    message: '请填写0~50之间的数字!'
                });
                this.Ability = oldVal
            }

            console.log(curVal, oldVal)
        },
        Professional: function(curVal, oldVal) {

            if (!curVal) {
                return ;
            }

            curVal = curVal.replace(/[^\d]+/g, '') //专业能力成果
            if (!curVal) {
                this.Professional = oldVal;
            }

            if (parseInt(this.Professional) < 0 || parseInt(this.Professional) > 50) {
                console.log(this.Professional)
                this.$messagebox({
                    title: '温馨提示',
                    message: '请填写0~50之间的数字!'
                });
                this.Professional = oldVal
            }

            console.log(curVal, oldVal)
        },
    }
});

$(window).on('hashchange', function(e) {
    var id = location.hash.substr(1);
    $(".main").hide()
    $("#" + id).show()
})
