<?php exit ();?>
<zh-cn: name>影评</zh-cn: name>
<zh-cn: definition>
<table width="90%" style="text-align: left">
<tr><td style="width: 30%">海报：</td><td style="width: 70%"><input type="text" id="tpl-0" placeholder="输入海报图片URL" class="inputLine inputLarge" value="http://" /></td></tr>
<tr><td>名称：</td><td><input type="text" id="tpl-1" placeholder="在此键入影片名" class="inputLine inputLarge"/></td></tr>
<tr><td>导演：</td><td><input type="text" id="tpl-2" placeholder="在此键入导演" class="inputLine inputLarge"/></td></tr>
<tr><td>主演：</td><td><input type="text" id="tpl-3" placeholder="在此键入主演" class="inputLine inputLarge"/></td></tr>
<tr><td>类型：</td><td><select class="selectLine" id="tpl-4" style="width: 80%;">
<option value="动作">动作</option>
<option value="爱情">爱情</option>
<option value="科幻">科幻</option>
<option value="恐怖">恐怖</option>
<option value="喜剧">喜剧</option>
<option value="悬疑">悬疑</option>
<option value="动画">动画</option>
<option value="犯罪">犯罪</option>
<option value="冒险">冒险</option>
<option value="纪实">纪实</option>
<option value="家庭">家庭</option>
<option value="魔幻">魔幻</option>
<option value="黑色">黑色</option>
<option value="历史">历史</option>
<option value="音乐">音乐</option>
<option value="歌舞">歌舞</option>
<option value="戏剧">戏剧</option>
<option value="传记">传记</option>
<option value="体育">体育</option>
<option value="战争">战争</option>
</select></td></tr>
<tr><td>上映时间：</td><td><input type="text" id="tpl-5" placeholder="在此输入上映时间" class="inputLine inputLarge"/></td></tr>
<tr><td>片长：</td><td><input type="text" id="tpl-6" placeholder="在此键入片长" class="inputLine inputLarge"/></td></tr>
<tr><td>IMDB链接：</td><td><input type="text" id="tpl-7" placeholder="在此键入IMDB链接" class="inputLine inputLarge" value="http://www.imdb.com/title/tt"/></td></tr>
<tr><td>评分：</td><td><select class="selectLine" id="tpl-8" style="width: 80%;">
<option value="★★★★★">5 - 力荐</option>
<option value="★★★★☆">4 - 推荐</option>
<option value="★★★☆☆">3 - 还行</option>
<option value="★★☆☆☆">2 - 较差</option>
<option value="★☆☆☆☆">1 - 很差</option>
</select></td></tr>
<tr><td>剧情简介：</td>
<td><textarea class="inputLine inputLarge" id="tpl-9" style="width: 80%; height: 120px"></textarea></td></tr></table>
请返回正文区域输入具体影评。<br>
<button class="buttonLine" onclick="$('#aContent').insertContent('## '+$('#tpl-1').val()+'\r\n\r\n![-L]('+$('#tpl-0').val()+') **导演：**'+$('#tpl-2').val()+'\r\n**主演**'+$('#tpl-3').val()+'\r\n**类型：**'+$('#tpl-4').val()+'\r\n**上映时间：**'+$('#tpl-5').val()+'\r\n**片长：**'+$('#tpl-6').val()+'\r\n**IMDB链接：**'+$('#tpl-7').val()+'\r\n**评分：**'+$('#tpl-8').val()+'\r\n\r\n**评论：**\r\n');lightboxLoaderDestroy ();"><span class="icon-disk"></span></button> 插入
<button class="buttonLine" onclick="lightboxLoaderDestroy ();"><span class="icon-cross"></span></button> <span style="color: red">取消</span>
</zh-cn: definition>

<en: name>Film Review</en: name>
<en: definition>
<table width="90%" style="text-align: left">
<tr><td style="width: 30%">Poster: </td><td style="width: 70%"><input type="text" id="tpl-0" placeholder="Poster Pic URL" class="inputLine inputLarge" value="http://" /></td></tr>
<tr><td>Title: </td><td><input type="text" id="tpl-1" placeholder="Film title" class="inputLine inputLarge"/></td></tr>
<tr><td>Director: </td><td><input type="text" id="tpl-2" placeholder="Film director" class="inputLine inputLarge"/></td></tr>
<tr><td>Stars: </td><td><input type="text" id="tpl-3" placeholder="" class="inputLine inputLarge"/></td></tr>
<tr><td>Type: </td><td><select class="selectLine" id="tpl-4" style="width: 80%;">
<option value="Action">Action</option>
<option value="Romance">Romance</option>
<option value="Sci-Fi">Sci-Fi</option>
<option value="Thriller">Thriller</option>
<option value="Comedy">Comedy</option>
<option value="Mystery">Mystery</option>
<option value="Animation">Animation</option>
<option value="Crime">Crime</option>
<option value="Adventure">Adventure</option>
<option value="Documentary">Documentary</option>
<option value="Family">Family</option>
<option value="Magic">Magic</option>
<option value="Film Noir">Film Noir</option>
<option value="History">History</option>
<option value="Musical">Musical</option>
<option value="Dance">Dance</option>
<option value="Opera">Opera</option>
<option value="Biography">Biography</option>
<option value="Sports">Sports</option>
<option value="War">War</option>
</select></td></tr>
<tr><td>On-air date: </td><td><input type="text" id="tpl-5" placeholder="On-air date" class="inputLine inputLarge"/></td></tr>
<tr><td>Duration: </td><td><input type="text" id="tpl-6" placeholder="Film duration" class="inputLine inputLarge"/></td></tr>
<tr><td>IMDB Link:</td><td><input type="text" id="tpl-7" placeholder="IMDB Link" class="inputLine inputLarge" value="http://www.imdb.com/title/tt"/></td></tr>
<tr><td>Score: </td><td><select class="selectLine" id="tpl-8" style="width: 80%;">
<option value="★★★★★">5 - Must See</option>
<option value="★★★★☆">4 - Recommended</option>
<option value="★★★☆☆">3 - Good</option>
<option value="★★☆☆☆">2 - Bad</option>
<option value="★☆☆☆☆">1 - Disaster</option>
</select></td></tr>
<tr><td>Intro:</td>
<td><textarea class="inputLine inputLarge" id="tpl-9" style="width: 80%; height: 120px"></textarea></td></tr></table>
Return to the main text area for detailed review.<br>
<button class="buttonLine" onclick="$('#aContent').insertContent('## '+$('#tpl-1').val()+'\r\n\r\n![-L]('+$('#tpl-0').val()+') **Director: **'+$('#tpl-2').val()+'\r\n**Stars: **'+$('#tpl-3').val()+'\r\n**Type: **'+$('#tpl-4').val()+'\r\n**On-air: **'+$('#tpl-5').val()+'\r\n**Duration: **'+$('#tpl-6').val()+'\r\n**IMDB: **'+$('#tpl-7').val()+'\r\n**Score: **'+$('#tpl-8').val()+'\r\n\r\n**Review: **\r\n');lightboxLoaderDestroy ();"><span class="icon-disk"></span></button> Insert
<button class="buttonLine" onclick="lightboxLoaderDestroy ();"><span class="icon-cross"></span></button> <span style="color: red">Cancel</span>
</en: definition>

<zh-tw: name>影評</zh-tw: name>
<zh-cn: definition>
<table width="90%" style="text-align: left">
<tr><td style="width: 30%">海報：</td><td style="width: 70%"><input type="text" id="tpl-0" placeholder="輸入海報圖片URL" class="inputLine inputLarge" value="http://" /></td></tr>
<tr><td>名稱：</td><td><input type="text" id="tpl-1" placeholder="在此鍵入影片名" class="inputLine inputLarge"/></td></tr>
<tr><td>導演：</td><td><input type="text" id="tpl-2" placeholder="在此鍵入導演" class="inputLine inputLarge"/></td></tr>
<tr><td>主演：</td><td><input type="text" id="tpl-3" placeholder="在此鍵入主演" class="inputLine inputLarge"/></td></tr>
<tr><td>類型：</td><td><select class="selectLine" id="tpl-4" style="width: 80%;">
<option value="動作">動作</option>
<option value="愛情">愛情</option>
<option value="科幻">科幻</option>
<option value="恐怖">恐怖</option>
<option value="喜劇">喜劇</option>
<option value="懸疑">懸疑</option>
<option value="動畫">動畫</option>
<option value="犯罪">犯罪</option>
<option value="冒險">冒險</option>
<option value="紀實">紀實</option>
<option value="家庭">家庭</option>
<option value="魔幻">魔幻</option>
<option value="黑色">黑色</option>
<option value="歷史">歷史</option>
<option value="音樂">音樂</option>
<option value="歌舞">歌舞</option>
<option value="戲劇">戲劇</option>
<option value="傳記">傳記</option>
<option value="體育">體育</option>
<option value="戰爭">戰爭</option>
</select></td></tr>
<tr><td>上映時間：</td><td><input type="text" id="tpl-5" placeholder="在此輸入上映時間" class="inputLine inputLarge"/></td></tr>
<tr><td>片長：</td><td><input type="text" id="tpl-6" placeholder="在此鍵入片長" class="inputLine inputLarge"/></td></tr>
<tr><td>IMDB連結：</td><td><input type="text" id="tpl-7" placeholder="在此鍵入IMDB連結" class="inputLine inputLarge" value="http://www.imdb.com/title/tt"/></td></tr>
<tr><td>評分：</td><td><select class="selectLine" id="tpl-8" style="width: 80%;">
<option value="★★★★★">5 - 力薦</option>
<option value="★★★★☆">4 - 推薦</option>
<option value="★★★☆☆">3 - 還行</option>
<option value="★★☆☆☆">2 - 較差</option>
<option value="★☆☆☆☆">1 - 很差</option>
</select></td></tr>
<tr><td>劇情簡介：</td>
<td><textarea class="inputLine inputLarge" id="tpl-9" style="width: 80%; height: 120px"></textarea></td></tr></table>
請返回正文區域輸入具體影評。<br>
<button class="buttonLine" onclick="$('#aContent').insertContent('## '+$('#tpl-1').val()+'\r\n\r\n![-L]('+$('#tpl-0').val()+') **導演：**'+$('#tpl-2').val()+'\r\n**主演**'+$('#tpl-3').val()+'\r\n**類型：**'+$('#tpl-4').val()+'\r\n**上映時間：**'+$('#tpl-5').val()+'\r\n**片長：**'+$('#tpl-6').val()+'\r\n**IMDB連結：**'+$('#tpl-7').val()+'\r\n**評分：**'+$('#tpl-8').val()+'\r\n\r\n**評論：**\r\n');lightboxLoaderDestroy ();"><span class="icon-disk"></span></button> 插入
<button class="buttonLine" onclick="lightboxLoaderDestroy ();"><span class="icon-cross"></span></button> <span style="color: red">取消</span>
</zh-tw: definition>

