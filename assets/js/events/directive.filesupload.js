/**
 * Created by Scrun3r-pc on 27-Feb-17.
 */

(function(){
   angular.module('evnts').directive('filesUpload', function () {
      return {
         scope: true,        //create a new scope
         link: function (scope, el, attrs) {
            el.bind('change', function (event) {
               var files = event.target.files;
               //iterate files since 'multiple' may be specified on the element
               /*for (var i = 0;i<files.length;i++) {
                  //emit event upward
                  //scope.$emit("fileSelected", { file: files[i] });
               }*/
               scope.$emit("filesSelected", { files: files });
            });
         }
      };
   });
})();
