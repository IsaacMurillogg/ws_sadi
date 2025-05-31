const URL_HOST = 'https://masa-concretos.wsmprastreo.com.mx/';
const URL_LOCAL = 'http://localhost/masa-concretos/public/';
function almacenarUnidades(arrayUnidades) {
    $.ajax({
        type: "POST",
        url: URL_HOST + 'api/v1/historialUnidad',
        headers: {
            'Authorization': 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI3IiwianRpIjoiNWNiYjA4ZjJjOTFkNzRhNzRhYmVhNjdhYTBlMjczYmM0ZmUxZTljNDQwNWIyYTRiMTQ4OGUzZDMwYzIxZWFlNDI4ZjRkMTUzYmNkMmM0YzQiLCJpYXQiOjE2Mzg1NTU3MDQuNjkwNTc1LCJuYmYiOjE2Mzg1NTU3MDQuNjkwNTg1LCJleHAiOjE2NzAwOTE3MDQuNTg5NDc0LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.OM0oCDZVH6xnXYFmsHb3kogF88_9e_mCbnYxkioiGzzfK0s1grG8AeYyDC5_C9nMGT3tjygCA4g0O5cYB-721a6ACmZkmCl5GDFkaHyWrDeJdVUTBXmYc0ucPZ9C5knpWcGldUyfGZlxBaT1Bc4dQSJSt0TNB5TKq_xY_TEiouw4YBZdeDELltvzztpIuwAA5wv6u971Mti2ckW26znYbHEJcUyPMbLTxvMbtkDsEPcnHBaMsX_4lf4rgY2n4aF0aVRpI94w1KimAL9kLj9Feaipg-18EhVbQXxgQ6mFtBTHb5Y2EGNEWYEJwM-ZEPa11phUwYWNqU6I2lp2QaCbqGcLfuh_9JHuruMRILg4L16YDQOaf3KCQy4eQFGWjqF4HpQUj4p_Pr2E_ANTmHGNQ9_awrvcq9NxNxI1VUR_Y6xjZ_LTFwbezagCKhGxSNTwjstO9BZQThmnTg5_8Um5sQAH9gUL-p4r4Qw7ajDA0c0hs4HMLE5L5V5aBt1pCSy8Grx2k1cMROoxUPZP8kWEJhJU4FKWeEke4fOryZH2_M7-jpW_teCvTwmNOXgoIvh_p4guZKVjHfMqAkGxGg8qF3bLL228zlq7WE2tM14tm0nbAwgsAr48P7wOydJVXdg0SJy8WJWRPODkWqImujCp3T90rW_mukOh9MkYsjHI4Do'
        },
        dataType: 'json',
        data: {
            jsonUnidades:JSON.stringify(arrayUnidades)
        }
    }).done(function(response) {
        console.log(response);
    }).fail(function(error) {
        console.log(error);
    });
}

$(document).ready(function() {
   setTimeout(()=>{
        location.reload();
    },60000);
    wialon.core.Session.getInstance().initSession("https://hst-api.wialon.com");
    wialon.core.Session.getInstance().loginToken(
        "da94700ba41f18e84461dc3e715464a877F5D87904756FBA261EFA6ABD1B111D1164C016", "",
        function(code) {
            if (code) {
                return;
            }
            init();
        });
});

function init() {
    var sess = wialon.core.Session.getInstance();
    var flags = wialon.item.Unit.dataFlag.lastPosition | wialon.item.Item.dataFlag.base | wialon.item.Unit.dataFlag
        .sensors | wialon.item.Unit.dataFlag.counters | wialon.item.Unit.dataFlag.lastMessage | wialon.item.Item
        .dataFlag.base | wialon.util.Number.or(wialon.item.Item.dataFlag.base, wialon.item.Item.dataFlag
            .customFields, wialon.item.Item.dataFlag.adminFields);
    sess.loadLibrary("unitSensors");
    sess.updateDataFlags([{
            type: "type",
            data: "avl_unit",
            flags: flags,
            mode: 0
        }],
        function(code) {
            var units = sess.getItems("avl_unit");
            if (!units || !units.length) {
                console.log("No units found");
                return;
            }

            let arrayUnidades = [];
            units.forEach(u => {
                var unidad = {
                    id_wialon: u.getId(),
                    name: u.getName(),
                    latitud: 0 ,
                    longitud: 0,
                    placa: 'NR',
                    lastMessage:'NA'
                }
                if(u['$$user_customFields'])
                    unidad.placa = u.$$user_customFields;
                if (u.$$user_lastMessage/* u.getPosition() */) {
                    var time = new Date(u.$$user_lastMessage.t * 1000);
                    unidad.latitud = u.$$user_lastMessage.pos.y;//u.getPosition().y;
                    unidad.longitud = u.$$user_lastMessage.pos.x;//u.getPosition().x;
                    unidad.lastMessage = time.toLocaleString().replace(new RegExp('/',"g") ,"-");

                }
                //console.log(unidad);
                arrayUnidades.push(unidad);
            });
            almacenarUnidades(arrayUnidades);
        }
    );
}
