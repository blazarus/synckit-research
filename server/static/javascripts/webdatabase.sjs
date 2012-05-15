/**
  @module webdatabase
  @desc A simple stratified wrapper over the asynchronous webdatabase api (webkit)
*/

/**
  @function openDatabase
*/
exports.openDatabase = function (name, version, desc, size) {
  var db = openDatabase(name, version, desc, size);
  return {
    executeSql: function (sql, params) {
      var rv;
      this.transaction(function(tx){
        rv = tx.executeSql(sql, params);
      });
      return rv;
    },
    transaction: function (cb) {
      var error;
      waitfor() {
        db.transaction(function(t) {
          var tx = {
            executeSql: function (sql, params) {
              waitfor(var dummytx, rv) {
                t.executeSql(sql, params, resume); 
              }
              return rv;
            }
          }
          cb(tx);
          resume();
        }, function (f) {
          error = f;
          resume();
        });
      }
      if (error) throw error;
    }
  };
}
