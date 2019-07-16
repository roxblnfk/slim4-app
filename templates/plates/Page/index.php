<?
/**
 * @var League\Plates\Template\Template $this
 * @var App\Model\Filesystem\FileInfo[] $images
 */
?>

<?= $this->insert('Chunk::navMenu') ?>
<?#= $this->insert('Chunk::uploader', ['url' => '/picture/upload']) ?>

Hello from Plates