var mysql = require('mysql');

var con = mysql.createPool({
    connectionLimit: 1000,
    connectTimeout: 60 * 60 * 1000,
    acquireTimeout: 60 * 60 * 1000,
    timeout: 60 * 60 * 1000,
    host: "us-cdbr-iron-east-05.cleardb.net",
    user: "b74e0ac591e038",
    password: "8b24d10f",
    database: "heroku_d38d2ba38f5ba8e"
    // host: "localhost",
    // user: "root",
    // password: "",
    // database: "coworkingspace"
});

con.on('error', function (err) {
    if (err) throw err;
});


module.exports = con;