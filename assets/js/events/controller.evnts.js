/**
 * Created by Scrun3r-pc on 25-Feb-17.
 */

(function(){

   angular.module('evnts').controller('evntsController',evntsController);

   function evntsController($scope, $http)
   {
      var vm = this;
      var dataService = $http;

      vm.pageHeader = 'Events';
      vm.events = [];
      vm.event = {};
      vm.selectedEvent = '';
      vm.stands = [];
      vm.files = {};
      vm.selectedStand = {};

      vm.initMap = initMap;
      vm.loadEvents = loadEvents;
      vm.setUIstate = setUIstate;
      vm.loadStands = loadStands;
      vm.loadStandDetail = loadStandDetail;
      vm.reserveStand = reserveStand;
      vm.cancelReservation = cancelReservation;
      vm.confirmReserv = confirmReserv;
      vm.userVisitModal = userVisitModal;
      vm.registerVisit = registerVisit;

      const pageMode = {
         "EVENTS" : "events",
         "HALL" : "hall",
         "RESERVATION" : "reservation"
      };

      vm.UIState = {
         mode : pageMode.EVENTS,
         isValid : true,
         eventsVisible : 1,
         hallVisible : 0,
         reservationVisible : 0,
         messages : []
      };

      //vm.eventsVisible = 1;
      //vm.hallVisible = 0;
      //vm.reservationVisible = 0;

      vm.company = {
         "company_id" : '',
         "company_name" : '',
         "email" : '',
         "phone" : '',
         "admin_email" : '',
         "logo" : ''
      }

      //an array of files selected
      $scope.company_logo = [];


      //listen for the file selected event
      $scope.$on("fileSelected", function (event, args) {
         $scope.$apply(function () {
            //add the file object to the scope's files collection
            //$scope.company_logo.push(args.file);
            $scope.company_logo = args.file;
            //
            //$scope.files.push(args.file);
            console.log($scope.company_logo)

         });
      });

      $scope.company_docs = [];

      //listen for the files selected event
      $scope.$on("filesSelected", function (event, args) {
         $scope.$apply(function () {
            //add the file object to the scope's files collection
            $scope.company_docs = args.files;
            //console.log($scope.filesCollection)
         });
      });

      vm.user = {
         email : 'user1@mailinator.com',
         password : 'sampleuser1',
      }

      function loadEvents()
      {
         var url = "index.php/events/events";
         dataService.get(url)
               .then(function(result){

                  if(result.status==200)vm.events = result.data;
                  populateEventsMarkers();

               },function(error){
                  alert('Something went wrong please try again');
               });

         console.log('in loadEvents');
      }

      function initMap()
      {
         console.log('in initMap');

         var uluru = {lat: -25.363, lng: 131.044};

         if(typeof google!="undefined"){

            vm.map = new google.maps.Map(document.getElementById('map-canvas'), {
               zoom: 4,
               center: uluru
            });
         }

         //can be called after every 15 sec. for latest data
         loadEvents();
      }

      function populateEventsMarkers()
      {
         console.log('populateEventsMarkers');

         if(vm.events.length && typeof google!=="undefined" && typeof vm.map!=="undefined")
         {
            var bounds = new google.maps.LatLngBounds();

            $.each(vm.events,function(i,evnt)
            {
               //ignore event without GPS coordinates
               if(!$.trim(evnt.gps_coordinates).length)return;//i.e. continue

               var coord = evnt.gps_coordinates.split(',');
               var pos = {lat: parseInt(coord[0]), lng: parseInt(coord[1])};

               var marker = new google.maps.Marker({
                  position: pos,
                  map: vm.map,
                  title: evnt.name
               });
               //todo: reset map bounds

               marker.eventObj = evnt;
               marker.eventIdx = i;

               google.maps.event.addListener(marker, 'click', function() {

                  if(!this.eventObj.name.length)return;

                  vm.event = this.eventObj;
                  vm.selectedEvent = this.eventIdx;
                  var $el = $('#selectedEvent');
                  $el.val(this.eventIdx);
                  angular.element($el).triggerHandler('change');
                  //vm.$apply(); //this triggers a $digest
                  $('#btn_bookevent').removeClass('disabled');

               });

               bounds.extend(marker.getPosition());

            });

            vm.map.fitBounds(bounds);

         }
         else
         {
            console.log('events or google does not exists')
         }
      }

      function setUIstate(mode)
      {
         //vm.selectedUIstate = mode;
         vm.UIState.mode = mode;

         vm.UIState.eventsVisible  = (mode===pageMode.EVENTS)?1:0;
         vm.UIState.hallVisible  = (mode===pageMode.HALL)?1:0;
         vm.UIState.reservationVisible = (mode===pageMode.RESERVATION)?1:0;

         if((mode===pageMode.EVENTS))
         {
            vm.pageHeader = "Events";
            //todo: after coming back to events map needs refresh
            var center = map.getCenter();
            google.maps.event.trigger(map, 'resize');
            vm.map.setCenter(center);
         }

         if((mode===pageMode.HALL))
         {
            vm.pageHeader = "Hall";//"("+vm.event.hall_name+")";
            vm.pageHeader = vm.event.hall_name;
            vm.pageHeader = vm.event.name+' > '+vm.event.hall_name;
         }

         if((mode===pageMode.RESERVATION))
         {
            vm.pageHeader = "Reservation";
            vm.pageHeader = vm.event.name+' > '+vm.event.hall_name+' > '+vm.selectedStand.name;
         }

      }

      function loadStands()
      {
         if(!vm.event.hall_id)return;

         setUIstate(pageMode.HALL);

         //var $btn = $('#btn_bookevent');

         var url = "index.php/events/loadStands?";//?
         url += "event_id="+ vm.event.event_id;
         url += "&hall_id="+ vm.event.hall_id;

         //loader
         //$('#stands_section').addClass('ajaxLoading');
         //$btn.addClass('disabled');
         $('#stands_section').prepend('<div class="ajax_loader" />');

         /*var data = {
            "event_id": vm.event.event_id,
            "hall_id": vm.event.hall_id,
         }*/

         dataService.get(url)
            .then(function(result){

               vm.stands = result.data.stands;
               vm.files = result.data.files;

            },function(error){

               alert('Something went wrong please try again');

            }).finally(function() {

               //$('#stands_section').removeClass('ajaxLoading');
               //$btn.removeClass('disabled');
               $('#stands_section .ajax_loader').remove();

            });

         console.log('in loadStands');
      }

      vm.baseURL = $('#baseurl').val();

      function loadStandDetail(idx)
      {
         if(vm.stands[idx].company_id)return;

         vm.selectedStand = vm.stands[idx];

         console.log('stand clicked');

         $('#standModal').modal('show');
      }

      function reserveStand()
      {
         $('#standModal').modal('hide');
         resetForm(null);
         setUIstate(pageMode.RESERVATION);
      }

      function resetForm(companyForm)
      {
         vm.company = {
            "company_id" : '',
            "company_name" : '',
            "email" : '',
            "phone" : '',
            "admin_email" : ''
         }

         if(companyForm)companyForm.$setPristine();
         vm.UIState.isValid = true;
         $('#company_logo').val('');
         $('#company_docs').val('');
      }

      function cancelReservation(companyForm)
      {
         resetForm(companyForm);
         setUIstate(pageMode.HALL);
      }

      function confirmReserv(companyForm)
      {
         if(!companyForm.$valid)
         {
            vm.UIState.isValid = false;
            return;
         }

         //custom validation
         if(!validateData())
         {
            //companyForm.$valid = false;
            return;
         }

         console.log(vm.company);
         //send Ajax Request to save
         //http://stackoverflow.com/questions/18571001/file-upload-using-angularjs
         //https://shazwazza.com/post/uploading-files-and-json-data-in-the-same-request-with-angular-js/
         saveReservation();


         companyForm.$setPristine();
         //resetForm(companyForm);

         //loadStands();
         //setUIstate(pageMode.HALL);
      }

      function saveReservation()
      {
         //extra check
         if(!vm.company.company_name)return;

         var url = "index.php/events/saveReservation";

         //prepare data
         var data = vm.company;
         data.event_id = vm.event.event_id;
         data.date_from = vm.event.date_from;
         data.date_to = vm.event.date_from;
         data.stand_id = vm.selectedStand.stand_id;

         console.log('in saveReserv',data,$scope.company_logo,$scope.company_docs);

         //var files = [];
         //files.push($scope.company_logo);

         //loader
         $('#companyFrorm').prepend('<div class="ajax_loader" />');

         var req = {
            method: 'POST',
            url: url,
            //IMPORTANT!!! You might think this should be set to 'multipart/form-data'
            // but this is not true because when we are sending up files the request
            // needs to include a 'boundary' parameter which identifies the boundary
            // name between parts in this multi-part request and setting the Content-type
            // manually will not set this boundary parameter. For whatever reason,
            // setting the Content-type to 'false' will force the request to automatically
            // populate the headers properly including the boundary parameter.
            headers: {'Content-Type': undefined},
            transformRequest: function (data) {
               var formData = new FormData();
               //need to convert our json object to a string version of json otherwise
               // the browser will do a 'toString()' on the object which will result
               // in the value '[Object object]' on the server.
               formData.append("model", angular.toJson(data.model));
               formData.append("logo", data.logo);
               //now add all of the assigned files
               for (var i = 0; i < data.files.length; i++) {
                  //add each file to the form data and iteratively name them
                  formData.append("file" + i, data.files[i]);
               }
               return formData;
            },
            //Create an object that contains the model and files which will be transformed
            // in the above transformRequest method
            data: { model: data, files: $scope.company_docs, logo: $scope.company_logo }
         };

         dataService(req)
            .then(function(result){

               console.log(result.data);

               //todo: show success msg
               if(result.status==200)loadStands();

            },function(error){

               //handleException(error);
               alert('Something went wrong please try again.\n'+error.data);
               //console.log(error);

            }).finally(function(){
               $('#companyFrorm .ajax_loader').remove();
         });
      }

      function addValidationMessage(prop, msg) {
         vm.UIState.messages.push({
            property: prop,
            message: msg
         });
      }

      function validateData() {

         vm.UIState.messages = [];

         if( !$('#company_logo').val() )
         {
            addValidationMessage('Logo','Logo must be seleted');
         }

         /*if( !$('#company_docs').val() )
         {
            addValidationMessage('Documents','Documents must be seleted');
         }*/

         //todo: js fileValidation required for docs

         console.log($scope.company_logo.type)

         var validImageFromat = ["image/gif","image/jpeg","image/jpg","image/png"];

         if($.inArray($scope.company_logo.type,validImageFromat)==-1)
         {
            addValidationMessage('Logo','Logo must be of type gif,jpeg,jpg or png');
         }

         validImageFromat.push('text/plain');
         for (var i = 0; i < $scope.company_docs.length; i++)
         {
            if($.inArray($scope.company_docs[i].type,validImageFromat)==-1)
            {
               addValidationMessage('Document','Documents must be of type gif,jpeg,jpg,png or txt');
               break;
            }
         }

         vm.UIState.isValid = (vm.UIState.messages.length == 0);

         return vm.UIState.isValid;
      }

      function handleException(error) {
         vm.UIState.isValid = false;
         var msg = {
            property: 'Error',
            message: ''
         };

         vm.UIState.messages = [];

         //temp
         msg.message = error.data;
         vm.UIState.messages.push(msg);
         console.log(error.data,vm.UIState)
         return;

         switch (error.status) {
            case 400:   // 'Bad Request'
               // Model state errors
               var errors = error.data.ModelState;
               debugger;

               // Loop through and get all
               // validation errors
               for (var key in errors) {
                  for (var i = 0; i < errors[key].length;
                       i++) {
                     addValidationMessage(key,
                           errors[key][i]);
                  }
               }

               break;

            case 404:  // 'Not Found'
               msg.message = 'The product you were ' +
                     'requesting could not be found';
               vm.uiState.messages.push(msg);

               break;

            case 500:  // 'Internal Error'
               msg.message =
                     error.data.ExceptionMessage;
               vm.uiState.messages.push(msg);

               break;

            default:
               msg.message = 'Status: ' +
                     error.status +
                     ' - Error Message: ' +
                     error.statusText;
               vm.uiState.messages.push(msg);

               break;
         }
      }

      function userVisitModal(stand_id)
      {
         if(!stand_id)return;

         vm.user.stand_id = stand_id;
         vm.user.event_id = vm.event.event_id;

         $('#userVisitModal').modal('show');
      }

      function registerVisit()
      {
         if( !vm.user.email || !vm.user.password )
         {
            console.log('email or password missing');
            return false;
         }

         //loader
         $('#btnUserSignIn').addClass('disabled');

         var url = 'index.php/events/register_visit?';
         url += 'email=' + vm.user.email ;
         url += '&pass=' + vm.user.password;
         url += '&event_id=' + vm.user.event_id;
         url += '&stand_id=' + vm.user.stand_id;

         //yup! although post is more safer :)
         dataService.get(url)
               .then(function(result){

                  alert('User visit count registered successfully!');
                  $('#userVisitModal').modal('hide');

               },function(error){

                  alert('Ooops! something went wrong! please try again.\n'+error.data)

               })
               .finally(function(){

                  $('#btnUserSignIn').removeClass('disabled');

               });
      }
   }

})();
