/**
 * Created by Scrun3r-pc on 27-Feb-17.
 */

(function(){
   angular.module('evnts').directive('fileUpload', function () {
      return {
         scope: true,        //create a new scope
         link: function (scope, el, attrs) {
            el.bind('change', function (event) {
               var files = event.target.files;
               scope.$emit("fileSelected", { file: files[0] });
            });
         }
      };
   });
})();
