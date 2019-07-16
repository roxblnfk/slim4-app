<?
/**
 * @var League\Plates\Template\Template $this
 */
?><html>
    <head>
        <meta charset="utf-8">

        <title><?= $this->e($title ?? '') ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- UIkit CSS -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/uikit/3.1.6/css/uikit.min.css" />

        <!-- UIkit JS -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/uikit/3.1.6/js/uikit.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/uikit/3.1.6/js/uikit-icons.min.js"></script>
    </head>
    <body>
        <script>
            function sendPostAjax(url, data) {
                // return a new promise.
                return new Promise(function(resolve, reject) {
                    // do the usual XHR stuff
                    var req = new XMLHttpRequest();
                    req.open('post', url);
                    // now we tell the server what format of post request we are making
                    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    req.onload = function() {
                        if (req.status === 200) {
                            resolve(req.response);
                        } else {
                            reject(Error(req.statusText));
                        }
                    };
                    // handle network errors
                    req.onerror = function() {
                        reject(Error("Network Error"));
                    }; // make the request
                    req.send(data);
                    //same thing if i hardcode like //req.send("limit=2");
                });
            }
        </script>
        <?= $this->section('content') ?>
    </body>
</html>
