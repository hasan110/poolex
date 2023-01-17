const express = require('express')
const http = require('http')
const app = express()
const server = http.createServer(app)

class Game {
    constructor() {
        this.server()
        this.gameInfo()
    }
    server() {
        global.io = require('socket.io')(server , {
          cors: {
            origin: "*"
          }
        });
        server.listen(3000, (err) => {
            if (err) {
                console.log(err);
            } else {
                console.log('the server run on Port', 3000);
            }
        })
    }
    gameInfo() {
        io.on('connection' , (client)=>{

            client.on('get_game_data', function(data) {
                io.emit('get_game_data', data)
            });

            client.on('start_game', function(data) {
                io.emit('start_game', data)
            });

            client.on('end_game', function(data) {
                io.emit('end_game', data)
            });
        })
    }
    route() {
        // app.get('/', (req, res) => {
        //     return res.render('index')
        // })
    }
}

module.exports = new Game()