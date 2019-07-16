<? /**
 * @var League\Plates\Template\Template $this
 * @var string $url
 */ ?>

<div class="js-upload uk-placeholder uk-text-center">
    <span uk-icon="icon: cloud-upload"></span>
    <span class="uk-text-middle">Attach binaries by dropping them here or</span>
    <div uk-form-custom>
        <input type="file" multiple>
        <span class="uk-link">selecting one</span>
    </div>
</div>

<progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>

<script>

    var bar = document.getElementById('js-progressbar');

    UIkit.upload('.js-upload', {

        url: '<?= str_replace('\'', '\\\'', $url ?? '') ?>',
        multiple: true,

        beforeSend: function () {
            // console.log('beforeSend', arguments);
        },
        beforeAll: function () {
            // console.log('beforeAll', arguments);
        },
        load: function () {
            // console.log('load', arguments);
        },
        error: function () {
            // console.log('error', arguments);
        },
        complete: function () {
            // console.log('complete', arguments);
        },

        loadStart: function (e) {
            // console.log('loadStart', arguments);

            bar.removeAttribute('hidden');
            bar.max = e.total;
            bar.value = e.loaded;
        },

        progress: function (e) {
            // console.log('progress', arguments);

            bar.max = e.total;
            bar.value = e.loaded;
        },

        loadEnd: function (e) {
            // console.log('loadEnd', arguments);

            bar.max = e.total;
            bar.value = e.loaded;
        },

        completeAll: function () {
            console.log('completeAll', arguments);

            setTimeout(function () {
                bar.setAttribute('hidden', 'hidden');
                document.location.reload();
            }, 1000);
        }

    });

</script>
