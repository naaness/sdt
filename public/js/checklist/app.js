var app = angular.module("app",["ngRoute"]);

app.filter('range', function() {
    return function(input, total) {
        total = parseInt(total);
        for (var i=0; i<total; i++)
            input.push(i);
        return input;
    };
});

app.config(["$routeProvider",function($routeProvider){
    $routeProvider.when("/",{
        templateUrl : "../templates/index.html",
        controller  : "indexCtrl"
    })
        .when("/about",{
            templateUrl : "templates/about.html",
            controller  : "aboutCtrl"
        })
        .when("/myProjects",{
            templateUrl : "templates/myProjects.html",
            controller  : "myProjectsCtrl"
        })
        .when("/contact",{
            templateUrl : "templates/contact.html",
            controller  : "contactCtrl"
        })
        .when("/labels",{
            templateUrl : "templates/labels.html",
            controller  : "labelsCtrl"
        })
}])

app.controller("indexCtrl", ["$scope","ServiceChecklist", function($scope,ServiceChecklist){
    $scope.title = "Checklist General";

    $scope.type1 = "normal";
    $scope.type2 = "mypro";
    $scope.type3 = "proje";
    $scope.type4 = "tome";
    $scope.type5 = "fromme";
    $scope.type6 = "tasks";
    $scope.sel_type = "normal";

    $scope.options = [
        { label: 'semana', value: 'week' },
        { label: 'mes', value: 'month' },
        { label: 'trimestre', value: 'trimestre' }
    ];

    $scope.title = "Checklist General";
    $scope.sdt_range_ini = 'mes';
    $scope.sdt_range = 'month';
    $scope.sdt_datetoday = moment.utc(fechaTod).format('DD/MM/YYYY');

    $scope.goToday = function(){
        showSpinner($("#tabla"));
        var promesa = ServiceChecklist.getMainData({ params: {range: $scope.sdt_range, date: $scope.sdt_datetoday, type:$scope.sel_type }});
        proccessData(promesa);
    };

    $scope.goToBack = function(){
        showSpinner($("#tabla"));
        var promesa = ServiceChecklist.getMainData({ params: {range: $scope.sdt_range, date: $scope.sdt_date_back, type:$scope.sel_type }});
        proccessData(promesa);
    };

    $scope.goToFrom = function(){
        showSpinner($("#tabla"));
        var promesa = ServiceChecklist.getMainData({ params: {range: $scope.sdt_range, date: $scope.sdt_date_from, type:$scope.sel_type }});
        proccessData(promesa);
    };

    $scope.goToType = function(type){
        if(type=='normal'){
            $scope.title = "Checklist General";
        }else if(type=='mypro'){
            $scope.title = "Mis proyectos";
        }else if(type=='proje'){
            $scope.title = "Proyectos";
        }else if(type=='tome'){
            $scope.title = "Delegadas a mi";
        }else if(type=='fromme'){
            $scope.title = "Delegadas por mi";
        }else if(type=='tasks'){
            $scope.title = "Mis tareas";
        }
        $scope.sel_type = type;
        showSpinner($("#tabla"));
        var promesa = ServiceChecklist.getMainData({ params: {range: $scope.sdt_range, date: dateOperate($scope.sdt_date_back,+1) ,type:type }});
        proccessData(promesa);
    };

    $scope.goToRange = function(type){
        showSpinner($("#tabla"));
        var promesa = ServiceChecklist.getMainData({ params: {range: $("#rangoVista").val(), date: dateOperate($scope.sdt_date_back,+1) ,type:$scope.sel_type }});
        proccessData(promesa);
    };

    var proccessData = function(promesa){
        promesa.then(function(data) {
            hideSpinner($("#tabla"));
            loadChecklist(data);
        },function(error) {
            alert("Error " + error);
        });
    }
    var loadChecklist = function(data){
        $scope.sdt_user_id = data.user_id;
        $scope.name_range = data.ranges.title;
        $scope.sdt_width = data.ranges.diff_days*35+56+56+250+56;
        $scope.sdt_date_back = dateOperate(data.ranges.start,-1);
        $scope.sdt_date_from = dateOperate(data.ranges.end,+1);
        setRangeStart(dateNewStandard(data.ranges.start));
        setRangeEnd(dateNewStandard(data.ranges.end));
        setUserId(data.user_id);
        setNDiff(data.ranges.diff_days);
        buildTable(data);
    }

    // Ejecutar la funcion de animacion
    showSpinner($("#tabla"));
    var promesa = ServiceChecklist.getMainData();
    proccessData(promesa);

}])
app.controller("myProjectsCtrl", ["$scope", function($scope){
    $scope.title = "myProjectsCtrl";
}])
app.controller("aboutCtrl", ["$scope", function($scope){
    $scope.message = "Acerca de";
}])
app.controller("blogCtrl", ["$scope",'ServicePosts', function($scope, ServicePosts){
    $scope.message = "Blog";

    var promesa = ServicePosts.getPosts();

    promesa.then(function(data)
        {
            $scope.posts = data.posts;
        }
        ,function(error)
        {
            alert("Error " + error);
        });
}])
app.controller("contactCtrl", ["$scope", function($scope){
    $scope.message = "Contacto";
}])
app.controller("labelsCtrl", ["$scope", function($scope){
    $scope.message = "Etiquetas";
}])

app.service('ServicePosts', ['$http', '$q', function($http, $q)
{
    this.getPosts = function()
    {
        var defer = $q.defer();

        $http.get("posts/get")
            .success(function(data)
            {
                defer.resolve(data);
            })
            .error(function(data)
            {
                defer.reject(data);
            });

        return defer.promise;
    }
}])
app.service('ServiceChecklist', ['$http', '$q', function($http, $q)
{
    this.getMainData = function(params)
    {
        var defer = $q.defer();

        $http.get(
            "checklist/sdtChecklistJson", params
            ).success(function(data){
                defer.resolve(data);
            })
            .error(function(data) {
                defer.reject(data);
            });

        return defer.promise;
    }
}])