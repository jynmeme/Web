<?php 
session_start();
require('lib/print.php');
$conn = mysqli_connect('localhost','root','test','level1');
mysqli_set_charset($conn, "utf8");

$list_num = 5;
$page_num = 5;

if (isset($_GET['vpage'])){
	$vpage = $_GET['vpage'];
} else {
	$vpage = 1;
}
$v_page = (int)$vpage;
$index_no = ($v_page - 1) * $list_num;

$query = "SELECT * FROM forum ORDER BY id DESC LIMIT {$index_no}, {$list_num}";
$query2 = "SELECT * FROM forum ORDER BY id DESC";

$data = mysqli_query($conn, $query);
$data2 = mysqli_query($conn, $query2);
$total_posts = mysqli_num_rows($data2);

$total_page = ceil($total_posts / $list_num);
$now_block = ceil($vpage / $page_num);

$s_pageNum = ($now_block - 1) * $page_num + 1;
if ($s_pageNum <= 0){
	$s_pageNum = 1;
}

$e_pageNum = (int)($now_block * $page_num);
if($e_pageNum > $total_page){
	$e_pageNum = $total_page;
}

$arr = array();
$descArr = array();
$arr2 = array();
$arr3 = array();

$i = 0;

while($row = mysqli_fetch_array($data)){
	$sql = "SELECT * FROM forum LEFT JOIN member ON forum.author_id = member.id WHERE forum.id = {$row['id']}";
	$result = mysqli_query($conn, $sql);
	$row_ = mysqli_fetch_array($result);
	
	$arr3[$i] = htmlspecialchars($row_['user_nick']);
	$escaped_title = htmlspecialchars($row['title']);
	$escaped_description = htmlspecialchars($row['description']);
	
	$arr[$i] = $escaped_title;
	$descArr[$i] = $escaped_description;
	$arr2[$i] = $row['id'];
	
	$i++;
}

if(isset($_GET['id'])){
	$filtered_id = mysqli_real_escape_string($conn, $_GET['id']);
	$sql = "SELECT * FROM forum WHERE id = {$filtered_id}";
	$result = mysqli_query($conn, $sql);
	$row = mysqli_fetch_array($result);
	$article['title'] = htmlspecialchars($row['title']);
	$article['description'] = htmlspecialchars($row['description']);
}
?>
<!doctype html>
<html>
<head>
	<title>
		<?php 
		echo $article['title'];
		?>
	</title>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="style.css"/>
	<script src="characters.js"></script>
</head>
<body>
	<h1 class="header"><a href="index.php">WEB</a></h1>
	<div id="grid">
		<div class="nav">
			<div class="nav_header">
				<div class="nav_post_btns" style="width: 195px;">
					<button class="nav_btn" type="button" onclick="location.href='create.php'">글쓰기</button>
				</div>
			</div>
		</div>
		<div id="article">
			<form action="update_process.php" method="post">
				<p>
					<?=$_SESSION['user_nick'];?>
				</p>
				<p>
					<input type="hidden" name="id" value="<?=$_GET['id']?>">
					<input class="title" type="text" name="title" placeholder="제목" onkeyup="characterCheck(this)" onkeydown="characterCheck(this)" onchange="characterCheck(this)" maxlength="30" value="<?=$article['title']?>">
				</p>
				<p>
					<textarea class="description" name="description" placeholder="내용"><?=$article['description']?></textarea>
				</p>
				<p>
					<input class="button" type="submit" value="작성 완료">
				</p>
			</form>
		</div>
		<div class="list">
			<?php
			$j = 0;
			while ($j < count($arr)){
				echo "<table class='list_layout' border=1; style='border-left: 0px black solid; border-right: 0px black solid;> 
						<tr style='border-width: 6px; border-style: solid;'>
							<td class='profile_label' rowspan='3' style='width:50px; border:0px;'> 
								<img class='profile_img' src='images/profiles/default/blank_profile_picture.png'/>
							</td>
							<td class='title_label' style='border:0px; font-weight: bold;' onclick=location.href='index.php?id={$arr2[$j]}'>
								{$arr[$j]}
							</td>
						</tr>
						<tr>
							<td class='desc_label' style='border:0px; font-size: 80%;' onclick=location.href='index.php?id={$arr2[$j]}'>
							{$descArr[$j]}
							</td>
						</tr>
						<tr>
							<td class='nick_label' style='border:0px; font-size: 70%;' onclick=location.href='index.php?id={$arr2[$j]}'>
								{$arr3[$j]}
							</td>
						</tr>
					</table>";
				$j++;
			}
			?>
		</div>
		<div class="pagination">
			<?php
			if($vpage <= 1){
    		?>
		 	<span onclick="location.href='update.php?id=<?php echo $_SESSION['curPost_id']?>&vpage=1'">이전</span>
			<?php } else{ ?>
			<span onclick="location.href='update.php?id=<?php echo $_SESSION['curPost_id'];?>&vpage=<?php echo ($vpage-1);?>'">이전</span>
			<?php }; 
			for ($i = $s_pageNum; $i<=$e_pageNum; $i++){
				echo "<span class='pageNum' onclick=location.href='update.php?id={$_SESSION['curPost_id']}&vpage={$i}'>{$i}</span>";
			}
			if($vpage >= $total_page) { ?>
			<span onclick="location.href='update.php?id=<?php echo $_SESSION['curPost_id']; ?>&vpage=<?php echo $total_page; ?>'">다음</span>
			<?php } else { ?>
			<span onclick="location.href='update.php?id=<?php echo $_SESSION['curPost_id']; ?>&vpage=<?php echo ($vpage+1); ?>'">다음</span>
			<?php } ?>
		</div>
	</div>
	<div class="footer">
		<form action="" method="get">
			<input id="darkmode" class="button" type="submit" value="Dark Mode" name="darkmode" onclick="ToggleTheme(this);">
		</form>
	</div>
	<script src="//code.jquery.com/jquery-3.3.1.js"></script>
	<script src="https://code.jquery.com/git/jquery-git.slim.js"></script>
	<script src="colors.js"></script>
</body>
</html>