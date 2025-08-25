<!DOCTYPE html>
<html>
<head>
    <title>취약한 Online Curl</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>
<body>
    <div class="container card" style="margin-top: 2rem;">
        <div class="card-content">
            <h1 class="title">Online Curl Request (Vulnerable)</h1>

            <?php
                // 'url' 파라미터가 있는지 확인
                if (isset($_GET['url'])) {
                    $url = $_GET['url'];

                    // URL이 'http'로 시작하는지 기본적인 검사
                    if (strpos($url, 'http') !== 0) {
                        die('http 프로토콜만 사용할 수 있습니다!');
                    } else {
                        // ❗️ 취약점 핵심 부분: escapeshellcmd()는 옵션 주입을 막지 못함
                        $command = 'curl ' . escapeshellcmd($url);
                        echo "<p><strong>실행된 명령어 (참고용):</strong> " . htmlentities($command) . "</p><hr>";

                        $result = shell_exec($command);

                        // 결과를 캐시 폴더에 저장
                        // 이 폴더는 미리 생성되어 있고 웹 서버가 쓰기 권한을 가지고 있어야 함
                        $cache_file = './cache/' . md5($url);
                        file_put_contents($cache_file, $result);

                        echo "<p><strong>캐시 파일:</strong> <a href='{$cache_file}'>{$cache_file}</a></p>";
                        echo "<h3>요청 결과:</h3>";
                        echo '<pre style="background-color: #f5f5f5; padding: 1em;">' . htmlentities($result) . '</pre>';
                    }
                } else {
                    // 'url' 파라미터가 없으면 입력 폼을 보여줌
            ?>
                    <form>
                        <div class="field">
                            <label class="label">URL</label>
                            <p class="help">명령어 주입을 테스트하려면 `http://example.com -o test.txt` 와 같이 입력하세요.</p>
                            <div class="control">
                                <input class="input" type="text" placeholder="https://example.com" name="url" required>
                            </div>
                        </div>
                        <div class="control">
                            <button class="button is-success" type="submit">요청 보내기</button>
                        </div>
                    </form>
            <?php
                } // if-else 끝
            ?>
        </div>
    </div>
</body>
</html>
