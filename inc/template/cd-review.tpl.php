<?php exit ();?>
<zh-cn: name>专辑点评</zh-cn: name>
<zh-cn: definition>
<table width="90%" style="text-align: left">
<tr><td style="width: 30%">封套：</td><td style="width: 70%"><input type="text" id="tpl-0" placeholder="输入封套图片URL" class="inputLine inputLarge" value="http://" /></td></tr>
<tr><td>专辑：</td><td><input type="text" id="tpl-1" placeholder="在此键入专辑名" class="inputLine inputLarge"/></td></tr>
<tr><td>表演者：</td><td><input type="text" id="tpl-2" placeholder="在此键入表演者" class="inputLine inputLarge"/></td></tr>
<tr><td>流派：</td><td><select class="selectLine" id="tpl-3" style="width: 80%;">
<option value="流行">流行</option>
<option value="摇滚">摇滚</option>
<option value="R&B">R&B</option>
<option value="舞曲">舞曲</option>
<option value="电子">电子</option>
<option value="民谣">民谣</option>
<option value="原声带">原声带</option>
<option value="爵士">爵士</option>
<option value="蓝调">蓝调</option>
<option value="乡村">乡村</option>
<option value="古典">古典</option>
<option value="纯音乐">纯音乐</option>
<option value="另类">另类</option>
<option value="Hip Hop">Hip Hop</option>
<option value="Indie">Indie</option>
<option value="拉丁">拉丁</option>
<option value="新世纪">新世纪</option>
<option value="歌剧">歌剧</option>
<option value="灵魂">灵魂</option>
<option value="雷鬼">雷鬼</option>
<option value="世界音乐">世界音乐</option>
<option value="曲艺">曲艺</option>
<option value="教育">教育</option>
</select></td></tr>
<tr><td>发行时间：</td><td><input type="text" id="tpl-4" placeholder="在此输入发行时间" class="inputLine inputLarge"/></td></tr>
<tr><td>评分：</td><td><select class="selectLine" id="tpl-5" style="width: 80%;">
<option value="★★★★★">5 - 力荐</option>
<option value="★★★★☆">4 - 推荐</option>
<option value="★★★☆☆">3 - 还行</option>
<option value="★★☆☆☆">2 - 较差</option>
<option value="★☆☆☆☆">1 - 很差</option>
</select></td></tr>
<tr><td>曲目：</td>
<td><textarea class="inputLine inputLarge" id="tpl-6" style="width: 80%; height: 120px"></textarea></td></tr></table>
请返回正文区域输入具体乐评。<br>
<button class="buttonLine" onclick="$('#aContent').insertContent('![-L]('+$('#tpl-0').val()+') **专辑：**'+$('#tpl-1').val()+'\r\n**表演者：**'+$('#tpl-2').val()+'\r\n**流派：**'+$('#tpl-3').val()+'\r\n**发行时间：**'+$('#tpl-4').val()+'\r\n**评分：**'+$('#tpl-5').val()+'\r\n\r\n**评论：**\r\n');lightboxLoaderDestroy ();"><span class="icon-disk"></span></button> 插入
<button class="buttonLine" onclick="lightboxLoaderDestroy ();"><span class="icon-cross"></span></button> <span style="color: red">取消</span>
</zh-cn: definition>

<en: name>CD Review</en: name>
<en: definition>
<table width="90%" style="text-align: left">
<tr><td style="width: 30%">Cover:</td><td style="width: 70%"><input type="text" id="tpl-0" placeholder="Cover pic URL" class="inputLine inputLarge" value="http://" /></td></tr>
<tr><td>Album:</td><td><input type="text" id="tpl-1" placeholder="Album name" class="inputLine inputLarge"/></td></tr>
<tr><td>Artist:</td><td><input type="text" id="tpl-2" placeholder="Artist name" class="inputLine inputLarge"/></td></tr>
<tr><td>Genre:</td><td><select class="selectLine" id="tpl-3" style="width: 80%;">
<option value="Pop">Pop</option>
<option value="Rock">Rock</option>
<option value="R&B">R&B</option>
<option value="Dance">Dance</option>
<option value="Electric">Electric</option>
<option value="Folk">Folk</option>
<option value="OST">OST</option>
<option value="Jazz">Jazz</option>
<option value="Blues">Blues</option>
<option value="Country">Country</option>
<option value="Classic">Classic</option>
<option value="Music">Music</option>
<option value="Alternative">Alternative</option>
<option value="Hip Hop">Hip Hop</option>
<option value="Indie">Indie</option>
<option value="Latin">Latin</option>
<option value="New Age">New Age</option>
<option value="Opera">Opera</option>
<option value="Soul">Soul</option>
<option value="Reggie">Reggie</option>
<option value="World">World</option>
</select></td></tr>
<tr><td>Date:</td><td><input type="text" id="tpl-4" placeholder="Release date" class="inputLine inputLarge"/></td></tr>
<tr><td>Score:</td><td><select class="selectLine" id="tpl-5" style="width: 80%;">
<option value="★★★★★">5 - Must Have</option>
<option value="★★★★☆">4 - Recommended</option>
<option value="★★★☆☆">3 - Good</option>
<option value="★★☆☆☆">2 - Bad</option>
<option value="★☆☆☆☆">1 - Disaster</option>
</select></td></tr>
<tr><td>Tracks:</td>
<td><textarea class="inputLine inputLarge" id="tpl-6" style="width: 80%; height: 120px"></textarea></td></tr></table>
Return to the main text area for detailed review.<br>
<button class="buttonLine" onclick="$('#aContent').insertContent('![-L]('+$('#tpl-0').val()+') **Album: **'+$('#tpl-1').val()+'\r\n**Artist: **'+$('#tpl-2').val()+'\r\n**Genre:  **'+$('#tpl-3').val()+'\r\n**Date:  **'+$('#tpl-4').val()+'\r\n**Score: **'+$('#tpl-5').val()+'\r\n\r\n**Review: **\r\n');lightboxLoaderDestroy ();"><span class="icon-disk"></span></button> Insert
<button class="buttonLine" onclick="lightboxLoaderDestroy ();"><span class="icon-cross"></span></button> <span style="color: red">Cancel</span>
</en: definition>

<zh-tw: name>專輯點評</zh-tw: name>
<zh-tw: definition>
<table width="90%" style="text-align: left">
<tr><td style="width: 30%">封套：</td><td style="width: 70%"><input type="text" id="tpl-0" placeholder="輸入封套圖片URL" class="inputLine inputLarge" value="http://" /></td></tr>
<tr><td>專輯：</td><td><input type="text" id="tpl-1" placeholder="在此鍵入專輯名" class="inputLine inputLarge"/></td></tr>
<tr><td>表演者：</td><td><input type="text" id="tpl-2" placeholder="在此鍵入表演者" class="inputLine inputLarge"/></td></tr>
<tr><td>流派：</td><td><select class="selectLine" id="tpl-3" style="width: 80%;">
<option value="流行">流行</option>
<option value="搖滾">搖滾</option>
<option value="R&B">R&B</option>
<option value="舞曲">舞曲</option>
<option value="電子">電子</option>
<option value="民謠">民謠</option>
<option value="原聲帶">原聲帶</option>
<option value="爵士">爵士</option>
<option value="藍調">藍調</option>
<option value="鄉村">鄉村</option>
<option value="古典">古典</option>
<option value="純音樂">純音樂</option>
<option value="另類">另類</option>
<option value="Hip Hop">Hip Hop</option>
<option value="Indie">Indie</option>
<option value="拉丁">拉丁</option>
<option value="新世紀">新世紀</option>
<option value="歌劇">歌劇</option>
<option value="靈魂">靈魂</option>
<option value="雷鬼">雷鬼</option>
<option value="世界音樂">世界音樂</option>
<option value="曲藝">曲藝</option>
<option value="教育">教育</option>
</select></td></tr>
<tr><td>發行時間：</td><td><input type="text" id="tpl-4" placeholder="在此輸入發行時間" class="inputLine inputLarge"/></td></tr>
<tr><td>評分：</td><td><select class="selectLine" id="tpl-5" style="width: 80%;">
<option value="★★★★★">5 - 力薦</option>
<option value="★★★★☆">4 - 推薦</option>
<option value="★★★☆☆">3 - 還行</option>
<option value="★★☆☆☆">2 - 較差</option>
<option value="★☆☆☆☆">1 - 很差</option>
</select></td></tr>
<tr><td>曲目：</td>
<td><textarea class="inputLine inputLarge" id="tpl-6" style="width: 80%; height: 120px"></textarea></td></tr></table>
請返回正文區域輸入具體樂評。<br>
<button class="buttonLine" onclick="$('#aContent').insertContent('![-L]('+$('#tpl-0').val()+') **專輯：**'+$('#tpl-1').val()+'\r\n**表演者：**'+$('#tpl-2').val()+'\r\n**流派：**'+$('#tpl-3').val()+'\r\n**發行時間：**'+$('#tpl-4').val()+'\r\n**評分：**'+$('#tpl-5').val()+'\r\n\r\n**評論：**\r\n');lightboxLoaderDestroy ();"><span class="icon-disk"></span></button> 插入
<button class="buttonLine" onclick="lightboxLoaderDestroy ();"><span class="icon-cross"></span></button> <span style="color: red">取消</span>
</zh-tw: definition>
