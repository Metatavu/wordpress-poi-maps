
module.exports = function(grunt) {
  require("load-grunt-tasks")(grunt);

  grunt.initConfig({
    "babel": {
      options: {
        sourceMap: true,
        minified: true
      },
      dist: {
        files: [{
          expand: true,
          cwd: "poi-maps/js",
          src: ["*.js"],
          dest: "poi-maps/js/bundle/",
          ext: ".min.js"
        }]
      }
    },
    "sass": {
      dist: {
        options: {
          style: "compressed"
        },
        files: [{
          expand: true,
          cwd: "poi-maps/styles",
          src: ["*.scss"],
          dest: "poi-maps/styles/css",
          ext: ".min.css"
        }]
      }
    },
  });
  
  grunt.registerTask("default", ["babel", "sass"]);
};