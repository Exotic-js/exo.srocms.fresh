<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="robots" content="index, follow" />
    <meta name="keywords" content="silkroad, Silkroad Online, MMORPG, Free to play, GameGami, online oyun, oyun, silk, gold, SRO, Bot, f2p, hardcore mmorpg, Online game, free online mmorpg, Free game, joymax, pc game, free download, download" />
    <meta name="Description" content="Silkroad Online dünyanın en çok oynanan ücretsiz MMORPG oyunlarının başında gelmektedir. Silkroad Online'da eski Çin, İslam ve Avrupa medeniyetlerinin derinliklerine gidecek ve PvP, zindan sistemleri, sonsuz kale savaşları ile en iyi kahramanlardan biri olmak için çarpışacaksınız!" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Silkroad Online - Player Ranking</title>
    <link href="/webapps/ranking/images/favicon.png" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <!-- Coded by m1xawy -->
    <link href="/webapps/ranking/css/style.css" rel="stylesheet">
</head>
<body>
<script type="text/javascript">
    function blankurl(val)
    {
        window.open(val, '_blank');
    }
</script>

<div id="rankmain">
    <div id="rankmenu_container">
        <ul>
            <li  class="selected"><a href="#">Player</a></li>
            <li ><a href="#">Guild</a></li>
            <li ><a href="#">Unique</a></li>
            <li ><a href="#">Level</a></li>
            <li ><a href="#">Fortress War(Player)</a></li>
            <li ><a href="#">Fortress War(Guild)</a></li>
        </ul>
    </div>
    <table class="table_rank" cellpadding="0" cellspacing="0">
        <thead id="ranking-thead">
            <tr>
                <th class="th1"></th>
                <th class="th2">#</th>
                <th class="th3">Race</th>
                <th class="th4">Character</th>
                <th class="th5">Point</th>
                <th class="th6">Change</th>
            </tr>
        </thead>
        <tbody id="ranking-body">
            <tr><td colspan="6" style="text-align:center;">No data to display.</td></tr>
        </tbody>
    </table>

    <div id="button_website" onclick="blankurl('https://silkroad.gamegami.com')">Official Site</div>
</div>

<script>
    const theadConfig = {
        "player": ["", "#", "Race", "Character", "Point", "Change"],
        "guild": ["", "#", "Guild", "Point", "Change"],
        "unique": ["", "#", "Race", "Character", "Point", "Change"],
        "level": ["", "#", "Race", "Character", "Level", "Change"],
        "fortress-player": ["", "#", "Race", "Character", "Kill", "Change"],
        "fortress-guild": ["", "#", "Guild", "Kill", "Change"]
    };

    document.addEventListener("DOMContentLoaded", () => {

        const tabs = document.querySelectorAll("#rankmenu_container ul li");
        tabs.forEach((el, idx) => el.addEventListener("click", () => {
            tabs.forEach(li => li.classList.remove("selected"));
            el.classList.add("selected");

            const types = ['player','guild','unique','level','fortress-player','fortress-guild'];
            loadRanking(types[idx]);
        }));

        loadRanking('player'); // default
    });

    async function loadRanking(type) {
        const thead = document.getElementById("ranking-thead");
        const tbody = document.getElementById("ranking-body");
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Loading...</td></tr>`;

        // update thead dynamically
        thead.innerHTML = "<tr>" + theadConfig[type].map(h => `<th>${h}</th>`).join("") + "</tr>";

        try {
            const res = await fetch(`/api/ranking/${type}`);
            const result = await res.json();

            tbody.innerHTML = '';
            if (!result.data || result.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${theadConfig[type].length}" style="text-align:center;">No data to display.</td></tr>`;
                return;
            }

            result.data.slice(0, 20).forEach((row, index) => {
                let topImage = '';
                if (index < 3 && result.config.topImage) {
                    topImage = `<img src="/webapps/ranking/${result.config.topImage[index+1]}" />`;
                }

                let rowHtml = '';
                switch(type){
                    case 'player':
                        rowHtml = `
                        <tr onmouseover="this.style.background='#2e261e'" onmouseout="this.style.background='none'">
                            <td class="td1">${topImage}</td>
                            <td class="td2">${index+1}</td>
                            <td class="td3">${row.RefObjID > 2000 ? '<img src="/webapps/ranking/images/european.png" />' : '<img src="/webapps/ranking/images/chinese.png" />'}</td>
                            <td class="td4">${row.CharName16}</td>
                            <td class="td5">${row.ItemPoints ?? 0}</td>
                            <td class="td6"><center><img style="width:16px;height:16px" src="/webapps/ranking/images/nochange.png"></center></td>
                        </tr>`;
                        break;

                    case 'guild':
                        rowHtml = `
                        <tr onmouseover="this.style.background='#2e261e'" onmouseout="this.style.background='none'">
                            <td class="td1">${topImage}</td>
                            <td class="td2">${index+1}</td>
                            <td class="td4">${row.Name}</td>
                            <td class="td5">${row.ItemPoints ?? 0}</td>
                            <td class="td6"><center><img style="width:16px;height:16px" src="/webapps/ranking/images/nochange.png"></center></td>
                        </tr>`;
                        break;

                    case 'unique':
                        rowHtml = `
                        <tr onmouseover="this.style.background='#2e261e'" onmouseout="this.style.background='none'">
                            <td class="td1">${topImage}</td>
                            <td class="td2">${index+1}</td>
                            <td class="td3">${row.RefObjID > 2000 ? '<img src="/webapps/ranking/images/european.png" />' : '<img src="/webapps/ranking/images/chinese.png" />'}</td>
                            <td class="td4">${row.CharName16}</td>
                            <td class="td5">${row.Points ?? 0}</td>
                            <td class="td6"><center><img style="width:16px;height:16px" src="/webapps/ranking/images/nochange.png"></center></td>
                        </tr>`;
                        break;

                    case 'level':
                        rowHtml = `
                        <tr onmouseover="this.style.background='#2e261e'" onmouseout="this.style.background='none'">
                            <td class="td1">${topImage}</td>
                            <td class="td2">${index+1}</td>
                            <td class="td3">${row.RefObjID > 2000 ? '<img src="/webapps/ranking/images/european.png" />' : '<img src="/webapps/ranking/images/chinese.png" />'}</td>
                            <td class="td4">${row.CharName16}</td>
                            <td class="td5">${row.CurLevel ?? 0}</td>
                            <td class="td6"><center><img style="width:16px;height:16px" src="/webapps/ranking/images/nochange.png"></center></td>
                        </tr>`;
                        break;

                    case 'fortress-player':
                        rowHtml = `
                        <tr onmouseover="this.style.background='#2e261e'" onmouseout="this.style.background='none'">
                            <td class="td1">${topImage}</td>
                            <td class="td2">${index+1}</td>
                            <td class="td3">${row.RefObjID > 2000 ? '<img src="/webapps/ranking/images/european.png" />' : '<img src="/webapps/ranking/images/chinese.png" />'}</td>
                            <td class="td4">${row.CharName16 ?? '-'}</td>
                            <td class="td5">${row.GuildWarKill ?? 0}</td>
                            <td class="td6"><center><img style="width:16px;height:16px" src="/webapps/ranking/images/nochange.png"></center></td>
                        </tr>`;
                        break;

                    case 'fortress-guild':
                        rowHtml = `
                        <tr onmouseover="this.style.background='#2e261e'" onmouseout="this.style.background='none'">
                            <td class="td1">${topImage}</td>
                            <td class="td2">${index+1}</td>
                            <td class="td4">${row.Name}</td>
                            <td class="td5">${row.TotalKills ?? 0}</td>
                            <td class="td6"><center><img style="width:16px;height:16px" src="/webapps/ranking/images/nochange.png"></center></td>
                        </tr>`;
                        break;
                }

                tbody.innerHTML += rowHtml;
            });

        } catch(err) {
            console.error("Failed to load ranking:", err);
            tbody.innerHTML = `<tr><td colspan="${theadConfig[type].length}" style="text-align:center;">Error loading data</td></tr>`;
        }
    }
</script>

</body>
</html>
