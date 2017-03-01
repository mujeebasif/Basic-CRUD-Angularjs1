<?php
/**
 * Created by PhpStorm.
 * User: Scrun3r-pc
 * Date: 25-Feb-17
 * Time: 4:52 PM
 */

$this->load->helper('url');
$assets = base_url() . 'assets/';
$assetsJs = base_url() . 'assets/js/events/';
$assetsCss = base_url() . 'assets/css/events/';
?>

<script
   src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCzDZwamTLmG4v_goYQApaID9o6LyuclWM">
</script>

<script src="<?php echo $assetsJs; ?>angular1.6.2.js"></script>
<script src="<?php echo $assetsJs; ?>module.evnts.js"></script>
<script src="<?php echo $assetsJs; ?>directive.fileupload.js"></script>
<script src="<?php echo $assetsJs; ?>directive.filesupload.js"></script>
<script src="<?php echo $assetsJs; ?>controller.evnts.js"></script>
<link rel="stylesheet" href="<?php echo $assetsCss ?>events.css">

<!--========================: Angular App :====================================-->

<div ng-app="evnts" ng-controller="evntsController as ec" ng-init="ec.initMap()" >

   <!--========================: page Header :===============================-->

   <div class="page-header">
      <h1>
         {{ec.pageHeader}}
         <button type="button" class="btn btn-primary pull-right"
            ng-show="ec.UIState.hallVisible" ng-click="ec.setUIstate('events')" >Back</button>
      </h1>
   </div>

   <!--========================: Events section :===============================-->

   <div ng-show="ec.UIState.eventsVisible">

      <div class="row">
         <div class="col-md-12">
            <div id="map-canvas">Loading google map...</div>
         </div>
      </div>

      <div class="row event_details">
         <div class="col-md-10">

            <div class="event_detail" ng-show="ec.event.name.length">

               <div class="panel panel-info">
                  <div class="panel-heading">
                     <h3 class="panel-title">
                        {{ec.event.name}}
                     </h3>
                  </div>
                  <div class="panel-body panel_body">
                     <span>Address:</span> {{ec.event.address}} <br/>
                     <span>From:</span> {{ec.event.date_from}} <br/>
                     <span>To:</span> {{ec.event.date_to}} <br/>
                     <span>Hall:</span> {{ec.event.hall_name}}
                  </div>
               </div>

               <input type="text" class="hidden" id="selectedEvent" ng-model="ec.selectedEvent">

            </div>

         </div>
         <div class="col-md-2">

            <button type="button" id="btn_bookevent" class="btn btn-primary pull-right disabled"
                  ng-click="ec.loadStands()" >Book your place</button>

         </div>
      </div>

   </div>

   <!--========================: Hall section :===============================-->

   <div ng-show="ec.UIState.hallVisible" id="stands_section">

      <!--===================: stand content :=================-->

      <span ng-repeat="(k,stand) in ec.stands">

         <div class="panel panel_stand pull-left" ng-click="ec.loadStandDetail(k)"
              ng-class="{'panel-success':stand.company_id,'panel-info':!stand.company_id,'pointer':!stand.company_id}" >

            <div ng-show="stand.company_id">

               <div class="thumbnail">
                  <img src="{{ec.baseURL}}assets/uploads/companies/company{{stand.company_id}}/{{stand.logo}}"
                       alt="{{stand.company_name?stand.company_name:'Company'}} logo"
                       class="img-responsive center-block company_logo">
               </div>

               <div class="company_contacts thumbnail">
                  <div>
                     <span>Docs: </span>
                     <span ng-repeat="file in ec.files[stand.company_id]">
                        <a href="{{ec.baseURL}}index.php/events/download/?company_id={{stand.company_id}}&file_name={{file.file_name}}"
                           >{{file.file_name}}</a>
                     </span>
                     <i ng-hide="ec.files[stand.company_id].length">-</i>
                  </div>
                  <!--<span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>-->
                  <div><span>Email: </span>{{stand.email ? stand.email:'-'}} </div>
                  <div><span>Phone: </span>{{stand.phone ? stand.phone:'-'}}</div>
               </div>

            </div>

            <div class="thumbnail" ng-show="!stand.company_id">
               <span>Price: </span>
               {{stand.price?'$'+stand.price:'-'}}
            </div>

            <div class="panel-heading">
               <h3 class="panel-title">
                  {{stand.name}}
                  <span class="label label-success pull-right" ng-show="stand.company_id" >Booked</span>
                  <span class="label label-info pull-right" ng-show="!stand.company_id" >Avaiable</span>
                  <input value="Visit today!" class="btn btn-primary btn-xs pull-right" id="visit_stand"
                     style="margin-right: 5px;" type="button" ng-show="stand.company_id"
                      ng-click="ec.userVisitModal(stand.stand_id)"><!--data-toggle="modal" data-target="#userVisitModal"-->
               </h3>
            </div>
            <div class="panel-body panel_body">

               <span>Dimensions:</span> {{stand.dimensions}} <br/>
               <!--<span>Price:</span> ${{stand.price}}-->

            </div>

         </div>

      </span>

      <!--===================: Stand popup content :=================-->

      <!-- Modal -->
      <div class="modal fade" id="standModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Stand Detail</h4>
               </div>
               <div class="modal-body">

                  <div class="thumbnail">
                     <img src="{{ec.baseURL}}assets/uploads/stands/{{ec.selectedStand.stand_image}}"
                          alt="{{ec.selectedStand.name?ec.selectedStand.name:'Stand'}} image"
                          class="img-responsive center-block stand_image">
                  </div>

                  <div><span class="labl">Name:</span> {{ec.selectedStand.name}}</div>
                  <div><span class="labl">Dimensions:</span> {{ec.selectedStand.dimensions}}</div>
                  <div><span class="labl">Price:</span> ${{ec.selectedStand.price}}</div>
                  <div><span class="labl">Hall:</span> {{ec.event.hall_name}}</div>

               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" ng-hide="ec.selectedStand.company_id"
                     ng-class="{'disabled':ec.selectedStand.company_id}" ng-click="ec.reserveStand()"
                     >Reserve</button>
               </div>
            </div>
         </div>
      </div>

      <!--===================: User visit popup content :=================-->

      <!-- Modal -->
      <div class="modal fade" id="userVisitModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">Stand Visit</h4>
               </div>
               <div class="modal-body">

                  <form class="form-inline">
                     <div class="form-group">
                        <label class="sr-only" for="exampleInputEmail3">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail3" placeholder="Email"
                        ng-model="ec.user.email">
                     </div>
                     <div class="form-group">
                        <label class="sr-only" for="exampleInputPassword3">Password</label>
                        <input type="password" class="form-control" id="exampleInputPassword3" placeholder="Password"
                        ng-model="ec.user.password">
                     </div>
                     <button type="button" id="btnUserSignIn" class="btn btn-default" ng-click="ec.registerVisit()">Sign in</button>
                  </form>

               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
               </div>
            </div>
         </div>
      </div>

   </div>

   <!--========================: Reservation section :===============================-->

   <div ng-show="ec.UIState.reservationVisible">

      <form name="companyForm" novalidate id="companyFrorm" class="form-horizontal companyFrorm" enctype="multipart/form-data" >

         <!--=====================: Form errors :=====================-->

         <div ng-hide="ec.UIState.isValid || (companyForm.$valid && !companyForm.$pristine)" class="row">
            <div class="col-xs-12">

               <div class="alert alert-danger alert-dismissable" role="alert">

                  <!--<button type="button" class="close" data-dismiss="alert">
                     <span aria-hidden="true">&times;</span>
                     <span class="sr-only">Close</span>
                  </button>-->

                  <ul>
                     <li ng-show="companyForm.company_name.$error.required">
                        Name must be filled in.
                     </li>
                     <li ng-show="companyForm.email.$error.required">
                        Email must be filled in.
                     </li>
                     <li ng-show="!companyForm.email.$valid">
                        Please enter valid email addresss
                     </li>
                     <li ng-show="companyForm.phone.$error.required">
                        Phone must be filled in.
                     </li>
                     <li ng-show="companyForm.admin_email.$error.required">
                        Admin email must be filled in.
                     </li>
                     <li ng-show="!companyForm.admin_email.$valid">
                        Please enter valid admin email addresss
                     </li>

                     <li ng-show="companyForm.logo.$error.required">
                        Logo must be selected.
                     </li>
                     <li ng-show="companyForm.event_id.$error.required">
                        Event must be selected.
                     </li>
                  </ul>
               </div>
            </div>
         </div>

         <div ng-show="ec.UIState.messages.length && (!ec.UIState.isValid && !companyForm.$pristine)" class="row">
            <div class="col-xs-12">
               <div class="alert alert-danger alert-dismissable" role="alert">
                  <ul>
                     <li ng-repeat="msg in ec.UIState.messages">
                        {{msg.message}}
                     </li>
                  </ul>
               </div>
            </div>
         </div>



         <!--=====================: Form i/p :=====================-->

         <div class="form-group">
            <label for="company_name" class="col-sm-2 control-label">Name</label>
            <div class="col-sm-10">
               <input type="text" class="form-control" id="company_name" placeholder="Name" required
                  ng-model="ec.company.company_name" name="company_name">
            </div>
         </div>

         <div class="form-group">
            <label for="company_email" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-10">
               <input type="email" class="form-control" id="company_email" placeholder="Email" required
                  ng-model="ec.company.email" name="email">
            </div>
         </div>

         <div class="form-group">
            <label for="company_phone" class="col-sm-2 control-label">Phone</label>
            <div class="col-sm-10">
               <input type="text" class="form-control" id="company_phone" placeholder="Phone" required
                  ng-model="ec.company.phone" name="phone">
            </div>
         </div>

         <div class="form-group">
            <label for="company_email_admin" class="col-sm-2 control-label">Admin Email</label>
            <div class="col-sm-10">
               <input type="email" class="form-control" id="company_email_admin" placeholder="Admin Email" required
                  ng-model="ec.company.admin_email" name="admin_email">
            </div>
         </div>

         <div class="form-group">
            <label for="company_logo" class="col-sm-2 control-label">Logo</label>
            <div class="col-sm-10">
               <input type="file" file-upload class="form-controll" id="company_logo" required
                      ng-modell="company_logo" name="logo">
               {{$scope.company_logo}}
            </div>
         </div>

         <div class="form-group">
            <label for="company_docs" class="col-sm-2 control-label">Documents</label>
            <div class="col-sm-10">
               <input type="file" files-upload multiple id="company_docs" required
                      ng-modell="company_docs" name="company_docs">
               {{$scope.company_docs}}
            </div>
         </div>

         <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
               <button type="button" class="btn btn-primary"
                  ng-click="ec.confirmReserv(companyForm)">Confirm Reservation</button>
               <button type="button" class="btn btn-default"
                  ng-click="ec.cancelReservation(companyForm)">Cancel</button>
            </div>
         </div>

         <input type="hidden" ng-model="ec.event.event_id" name="event_id">
         <input type="hidden" ng-model="ec.event.date_from" name="date_from">
         <input type="hidden" ng-model="ec.event.date_to" name="date_to">

      </form>

   </div>

</div>

<input type="hidden" id="baseurl" value="<?php echo base_url(); ?>">

<style>
   .ajaxLoading {
      opacity: 0.3;
      background-image: url('<?php echo $assets ?>loader2.gif');
      background-repeat: no-repeat;
      background-position: center;
   }
   .ajax_loader {
      background-image: url(<?php echo $assets ?>loader2.gif);
      background-repeat: no-repeat;
      background-position: center center;
      background-color: #fff;
      position: absolute;
      top: 0;
      width: 100%;
      height: 100%;
      opacity: 0.7;
      z-index: 5;
   }
</style>