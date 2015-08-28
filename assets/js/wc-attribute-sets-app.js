app = angular.module( 'WCAttributeSets', [ 'ngRoute' ] );

// Routes
app.config(function($routeProvider, $locationProvider) {
  
  $routeProvider
  .when( '/', {
    templateUrl: wcAttributeSets.viewsUrl + 'list.html',
    controller: 'AttributeSetsListController'
  } ). when( '/AttributeSets/New', {
    templateUrl: wcAttributeSets.viewsUrl + 'new.html',
    controller: 'AttributeSetsNewController'
  }). when( '/AttributeSets/Edit/:attributeSetKey', {
    templateUrl: wcAttributeSets.viewsUrl + 'edit.html',
    controller: 'AttributeSetsEditController'
  } );

});

// Factory for list of all attribute sets
app.factory( 'attributeSetsListLoader', function( $q, $http, $cacheFactory ) {

    data = {
        action: 'wc_attribute_sets_get_all_sets'
    };

    var defer = $q.defer();

    $http({
        method: 'POST',
        data: jQuery.param(data),
        url: wcAttributeSets.ajaxUrl,
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        cache: false
    })
    .success( function( data, status, headers, config ) {
        defer.resolve(data);
    } )
    .error( function( data, status, headers, config ) {
        $scope.message = 'Failed.';
    } );

    return defer.promise;

} );

// Factory for edit attribute set
app.factory( 'attributeSetsEditLoader', function( $q, $http ) {
    
    return {
        getSet: function(setKey) {

            console.log('SET KEY');
            console.log(setKey);

            data = {
                action: 'wc_attribute_sets_get_set',
                'setKey': setKey
            };

            var defer = $q.defer();

            $http({
                method: 'POST',
                data: jQuery.param(data),
                url: wcAttributeSets.ajaxUrl,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            })
            .success( function( data, status, headers, config ) {
                defer.resolve(data);
            } )
            .error( function( data, status, headers, config ) {
                $scope.message = 'Failed.';
            } );

            return defer.promise;
        }
    };

} );

app.controller( 'AttributeSetsListController', function( $scope, $http, attributeSetsListLoader ) {

    // Get all the attribute sets here, the php is ready, should consider a service or sth here
    $scope.attributeSetsList = false;

    attributeSetsListLoader.then(function(data){
        $scope.attributeSetsList = data;
    });

} ); // /AttributeSetsNewController

app.controller( 'AttributeSetsEditController', function( $scope, $http, $routeParams, $location, attributeSetsEditLoader ) {

    $scope.attributeSet = false;
    $scope.productsAttributes = wcAttributeSets.productsAttributes;

    attributeSetsEditLoader.getSet($routeParams.attributeSetKey).then(function(data){
        console.log('GOT THE DATA');
        console.log(data);
        $scope.attributeSet = data;
    });

    $scope.updateAttributeSet = function()
    {
        data = {
            action: 'wc_attribute_sets_update_set',
            set: $scope.attributeSet,
            key: $routeParams.attributeSetKey
        };

        console.log( 'going to '  + wcAttributeSets.ajaxUrl );

        $http({
            method: 'POST',
            data: jQuery.param(data),
            url: wcAttributeSets.ajaxUrl,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .success( function( data, status, headers, config ) {
            console.log(data);
            $location.path('/');
        } )
        .error( function( data, status, headers, config ) {
            $scope.message = 'Failed.';
            console.log("Failed");
        } );
    }

    $scope.addAttribute = function()
    {
        $scope.attributeSet.attributes.push({ attribute_label: '', attribute_name: '' });
    };

} ); // /AttributeSetsEditController


app.controller( 'AttributeSetsNewController', function( $scope, $http, $location ) {

    $scope.attributeSet = {
        'name' : '',
        'attributes': []
    };

    $scope.productsAttributes = wcAttributeSets.productsAttributes;

    /**
     * Adds an attribute to the model so that another select box appears
     */
    $scope.addAttribute = function()
    {
        $scope.attributeSet.attributes.push({ attribute_label: '', attribute_name: '' });
    };

    /**
     * Saves the attribute set
     * @return {[type]} [description]
     */
    $scope.saveAttributeSet = function()
    {

        data = {
            action: 'wc_attribute_sets_create_set',
            set: $scope.attributeSet
        };

        console.log( 'going to '  + wcAttributeSets.ajaxUrl );

        $http({
            method: 'POST',
            data: jQuery.param(data),
            url: wcAttributeSets.ajaxUrl,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .success( function( data, status, headers, config ) {
            console.log(data);
            $location.path('/');
            window.location.reload();
        } )
        .error( function( data, status, headers, config ) {
            $scope.message = 'Failed.';
            console.log("Failed");
        } );
    };

} ); // /AttributeSetsNewController