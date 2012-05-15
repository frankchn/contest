<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'common.php');

class SpeedProblemConfigMetadata extends ProblemConfigMetadata {
  
  public function __construct($problem_id, $division_id, $contest_id, $metadata, $division_metadata) {
    parent::__construct($problem_id, $division_id, $contest_id, $metadata, $division_metadata);
    if (!isset($this->division_metadata['points'])) {
      $this->division_metadata['points'] = 0;
    }
    if (!isset($this->metadata['judge_io'])) {
      $this->metadata['judge_io'] = array();
    }
  }
  
  public function render() {
    parent::render();
    $pointsID = $this->transformID('points');
    $points = $this->division_metadata['points'];
    $downloadID = $this->transformID('download');
    $uploadID = $this->transformID('upload');
    $uploadFileID = $this->transformID('upload_file');
    $uploadFormID = $this->transformID('upload_form');
    $uploadFrameID = $this->transformID('upload_frame');
?>
<script type="text/javascript">
(function (problemID, divisionID, contestID, metadata, divisionMetadata) {
  $("#<?= $pointsID ?>").keydown(function() {
    $(this).addClass('updating');
  }).change(function() {
    var thisElem = $(this);
    divisionMetadata['points'] = thisElem.val();
    $.ajax({
      data: $.stringifyJSON({'action' : 'modify_problem', 'problem_id' : problemID, 'division_id' : divisionID, 'contest_id' : contestID, 'key' : 'division_metadata', 'value' : $.stringifyJSON(divisionMetadata)}),
      success: function(ret) {
        if (ret['success']) {
          thisElem.removeClass('updating').addClass('updated');
          setTimeout(function() { thisElem.removeClass('updated'); }, 500);
        }
      }
    });
  });
  $("#<?= $downloadID ?>").click(function() {
    window.location.href = "handlefile.php?action=download_speed_zip&problem_id=" + problemID + "&division_id=" + divisionID + "&contest_id=" + contestID;
  });
})(<?= $this->problem_id ?>, <?= $this->division_id ?>, <?= $this->contest_id ?>, <?= json_encode($this->metadata) ?>, <?= json_encode($this->division_metadata) ?>);
</script>
<table>
  <tr>
    <td>Point value:</td>
    <td><input id="<?= $pointsID ?>" type="text" value="<?= $points ?>"></input></td>
  </tr>
  <tr>
    <td>Judge in/out:</td>
    <td>
      <button id="<?= $downloadID ?>">Download</button>
      <form id="<?= $uploadFormID ?>" action="handlefile.php" method="post" enctype="multipart/form-data" target="<?= $uploadFrameID ?>">
        <input id="<?= $uploadFileID ?>" type="file" name="upload_file"></input> 
        <input id="<?= $uploadID ?>" type="submit" value="zip upload"></input>
        <input type="hidden" name="problem_id" value="<?= $this->problem_id ?>"></input>
        <input type="hidden" name="division_id" value="<?= $this->division_id ?>"></input>
        <input type="hidden" name="contest_id" value="<?= $this->contest_id ?>"></input>
        <input type="hidden" name="action" value="upload_speed_zip"></input>
        <br />
        <iframe name="<?= $uploadFrameID ?>" style="width: 300px; height: 30px; border: 0px;"></iframe>
      </form>
    </td>
  </tr>
</table>
<?php
// END RENDER
  }
}
?>