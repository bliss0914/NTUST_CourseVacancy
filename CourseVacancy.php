<?php
//設定網頁編碼為UTF8
header('Content-type: text/html; charset=utf-8');

//檢查是否有GET參數，有的話就當作API，沒有的話就當作是網頁
if (isset($_GET['no'])) {
    //預設的顯示結果
    $output = 'error';
    //取得課程代碼
    $course_no = trim($_GET['no']);
    //檢查課程代碼的長度
    if (strlen($course_no) == 9) {
        //長度為9才進行查詢
        $output = main($course_no);
    } else {
        //否則印出錯誤
        $output = '課程代碼長度錯誤，必須為9';
    }
    echo $output;
    exit();
}

/**
 * 查詢選課人數的餘額
 * @param string $course_no 課程代碼
 * @return string 結果的JSON
 */
function main($course_no) {
    //DEBUG MODE
    $DEBUG = null;

    //課程查詢的網址
    $queryURL = "http://140.118.31.215/querycourse/ChCourseQuery/QueryCondition.aspx";
    //課程查詢結果的網址
    $resultURL = "http://140.118.31.215/querycourse/ChCourseQuery/QueryResult.aspx";
    //特定課程的網址
    $courseURL = "http://140.118.31.215/querycourse/ChCourseQuery/DetailCourse.aspx?chooseCourseNo=$course_no";
    //網頁的COOKIE
    $cookie = '';

    //要POST的資料，必須每學期都更新，來源為課程查詢的網址
    $post = array(
        "__VIEWSTATE" => "/wEPDwULLTE2MjE5MDgyNzIPZBYCAgEPZBYEAh8PZBYEZg9kFgJmD2QWAmYPZBYCAgEPZBYCZg9kFgJmDw8WAh4HVmlzaWJsZWhkFgYCAg9kFghmD2QWAmYPEA8WCB4KRGF0YU1lbWJlcgUJRWR1X09yZ2FuHg1EYXRhVGV4dEZpZWxkBRFDb2xsZWdlRGVwYXJ0bWVudB4ORGF0YVZhbHVlRmllbGQFAk5PHgtfIURhdGFCb3VuZGdkEBUwCeS4jemBuOaThxboqK3oqIjlrbjpmaIt5bu656+J57O7IuioreioiOWtuOmZoi3libXmhI/oqK3oqIjlrbjlo6vnj60c6Kit6KiI5a246ZmiLeioreioiOeglOeptuaJgB/oqK3oqIjlrbjpmaIt5bel5ZWG5qWt6Kit6KiI57O7K+ioreioiOWtuOmZoi3libXmhI/oqK3oqIjlrbjlo6vlrbjkvY3lrbjnqIsc6Zu76LOH5a246ZmiLeizh+ioiuW3peeoi+ezuxzpm7vos4flrbjpmaIt6Zu76LOH5a245aOr54+tHOmbu+izh+WtuOmZoi3pm7vmqZ/lt6XnqIvns7si6Zu76LOH5a246ZmiLeWFiembu+W3peeoi+eglOeptuaJgBzpm7vos4flrbjpmaIt6Zu75a2Q5bel56iL57O7JeS6uuaWh+ekvuacg+WtuOmZoi3kurrmlofnpL7mnIPlrbjnp5El5Lq65paH56S+5pyD5a246ZmiLeW4q+izh+WfueiCsuS4reW/gyLkurrmlofnpL7mnIPlrbjpmaIt5oeJ55So5aSW6Kqe57O7H+S6uuaWh+ekvuacg+WtuOmZoi3pgJrorZjlrbjnp5Ec5Lq65paH56S+5pyD5a246ZmiLemrlOiCsuWupDHkurrmlofnpL7mnIPlrbjpmaIt5pW45L2N5a2457+S6IiH5pWZ6IKy56CU56m25omANOaZuuaFp+iyoeeUouWtuOmZoi3mmbrmhafosqHnlKLmrIrlrbjlo6vlrbjkvY3lrbjnqIsi5pm65oWn6LKh55Si5a246ZmiLeWwiOWIqeeglOeptuaJgDHmmbrmhafosqHnlKLlrbjpmaIt56eR5oqA566h55CG5a245aOr5a245L2N5a2456iLKOaZuuaFp+iyoeeUouWtuOmZoi3np5HmioDnrqHnkIbnoJTnqbbmiYAc566h55CG5a246ZmiLeS8gealreeuoeeQhuezuyvnrqHnkIblrbjpmaIt6LKh5YuZ6YeR6J6N5a245aOr5a245L2N5a2456iLIueuoeeQhuWtuOmZoi3osqHli5nph5Hono3noJTnqbbmiYAc566h55CG5a246ZmiLeW3pealreeuoeeQhuezuxDnrqHnkIblrbjpmaItTUJBHOeuoeeQhuWtuOmZoi3nrqHnkIblrbjlo6vnj60c566h55CG5a246ZmiLeeuoeeQhueglOeptuaJgBznrqHnkIblrbjpmaIt6LOH6KiK566h55CG57O7MeeyvuiqoOamruitveWtuOmZoi3mh4nnlKjnp5HmioDlrbjlo6vlrbjkvY3lrbjnqIsx57K+6Kqg5qau6K295a246ZmiLemGq+WtuOW3peeoi+WtuOWjq+WtuOS9jeWtuOeoiyjnsr7oqqDmpq7orb3lrbjpmaIt6Yar5a245bel56iL56CU56m25omAMeeyvuiqoOamruitveWtuOmZoi3oibLlvanoiIfnhafmmI7np5HmioDnoJTnqbbmiYBA57K+6Kqg5qau6K295a246ZmiLeiJsuW9qeW9seWDj+iIh+eFp+aYjuenkeaKgOWtuOWjq+WtuOS9jeWtuOeoiyjnsr7oqqDmpq7orb3lrbjpmaIt5oeJ55So56eR5oqA56CU56m25omAJeeyvuiqoOamruitveWtuOmZoi3kuI3liIbns7vlrbjlo6vnj60657K+6Kqg5qau6K295a246ZmiLeaHieeUqOenkeaKgOeglOeptuaJgOadkOaWmeenkeaKgOWtuOeoiyjlt6XnqIvlrbjpmaIt6Ieq5YuV5YyW5Y+K5o6n5Yi256CU56m25omAHOW3peeoi+WtuOmZoi3lt6XnqIvlrbjlo6vnj60c5bel56iL5a246ZmiLeWMluWtuOW3peeoi+ezuxzlt6XnqIvlrbjpmaIt54ef5bu65bel56iL57O7N+W3peeoi+WtuOmZoi3ntqDog73nlKLmpa3mqZ/pm7vlt6XnqIvlrbjlo6vlrbjkvY3lrbjnqIsc5bel56iL5a246ZmiLeapn+aisOW3peeoi+ezuyLlt6XnqIvlrbjpmaIt5p2Q5paZ56eR5oqA56CU56m25omAHOW3peeoi+WtuOmZoi3ot6jns7vmiYDlrbjnqIs65bel56iL5a246ZmiLeW3peeoi+aKgOihk+eglOeptuaJgOaKgOiBt+WwiOalreeZvOWxleWtuOeoiyXlt6XnqIvlrbjpmaIt5p2Q5paZ56eR5a246IiH5bel56iL57O7A0FMTBUwBE5VTEwENC1BRAQ0LUNEBDQtREUENC1EVAQ0LURYBDItQ1MEMi1FQwQyLUVFBDItRU8EMi1FVAQ1LUNDBDUtRVAENS1GTAQ1LUdFBDUtUEUENS1WRQQ2LUlCBDYtUEEENi1UQgQ2LVRNBDMtQkEEMy1GQgQzLUZOBDMtSU0EMy1NQQQzLU1CBDMtTUcEMy1NSQQwLUFUBDAtQkIEMC1CRQQwLUNJBDAtQ1gEMC1FTgQwLUhDBDAtTVMEMS1BQwQxLUNFBDEtQ0gEMS1DVAQxLUdYBDEtTUUEMS1NUwQxLVJTBDEtVFYEMS1UWAFfFCsDMGdnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZxYBZmQCAQ9kFgJmDxAPFggfAQUJRWR1X09yZ2FuHwIFBlBlcmlvZB8DBQJOTx8EZ2QQFQEJ5LiN6YG45pOHFQEETlVMTBQrAwFnFgFmZAICD2QWAmYPEA8WCB8BBQlFZHVfT3JnYW4fAgUFZ3JhZGUfAwUCTk8fBGdkEBUBCeS4jemBuOaThxUBBE5VTEwUKwMBZxYBZmQCAw9kFgJmDxAPFggfAQUJRWR1X09yZ2FuHwIFBUNsYXNzHwMFAk5PHwRnZBAVAQnkuI3pgbjmk4cVAQROVUxMFCsDAWcWAWZkAgMPZBYIZg9kFgJmDxAPFggfAQUJRWR1X09yZ2FuHwIFEUNvbGxlZ2VEZXBhcnRtZW50HwMFAk5PHwRnZBAVMAnkuI3pgbjmk4cW6Kit6KiI5a246ZmiLeW7uuevieezuyLoqK3oqIjlrbjpmaIt5Ym15oSP6Kit6KiI5a245aOr54+tHOioreioiOWtuOmZoi3oqK3oqIjnoJTnqbbmiYAf6Kit6KiI5a246ZmiLeW3peWVhualreioreioiOezuyvoqK3oqIjlrbjpmaIt5Ym15oSP6Kit6KiI5a245aOr5a245L2N5a2456iLHOmbu+izh+WtuOmZoi3os4foqIrlt6XnqIvns7sc6Zu76LOH5a246ZmiLembu+izh+WtuOWjq+ePrRzpm7vos4flrbjpmaIt6Zu75qmf5bel56iL57O7Iumbu+izh+WtuOmZoi3lhYnpm7vlt6XnqIvnoJTnqbbmiYAc6Zu76LOH5a246ZmiLembu+WtkOW3peeoi+ezuyXkurrmlofnpL7mnIPlrbjpmaIt5Lq65paH56S+5pyD5a2456eRJeS6uuaWh+ekvuacg+WtuOmZoi3luKvos4fln7nogrLkuK3lv4Mi5Lq65paH56S+5pyD5a246ZmiLeaHieeUqOWkluiqnuezux/kurrmlofnpL7mnIPlrbjpmaIt6YCa6K2Y5a2456eRHOS6uuaWh+ekvuacg+WtuOmZoi3pq5TogrLlrqQx5Lq65paH56S+5pyD5a246ZmiLeaVuOS9jeWtuOe/kuiIh+aVmeiCsueglOeptuaJgDTmmbrmhafosqHnlKLlrbjpmaIt5pm65oWn6LKh55Si5qyK5a245aOr5a245L2N5a2456iLIuaZuuaFp+iyoeeUouWtuOmZoi3lsIjliKnnoJTnqbbmiYAx5pm65oWn6LKh55Si5a246ZmiLeenkeaKgOeuoeeQhuWtuOWjq+WtuOS9jeWtuOeoiyjmmbrmhafosqHnlKLlrbjpmaIt56eR5oqA566h55CG56CU56m25omAHOeuoeeQhuWtuOmZoi3kvIHmpa3nrqHnkIbns7sr566h55CG5a246ZmiLeiyoeWLmemHkeiejeWtuOWjq+WtuOS9jeWtuOeoiyLnrqHnkIblrbjpmaIt6LKh5YuZ6YeR6J6N56CU56m25omAHOeuoeeQhuWtuOmZoi3lt6Xmpa3nrqHnkIbns7sQ566h55CG5a246ZmiLU1CQRznrqHnkIblrbjpmaIt566h55CG5a245aOr54+tHOeuoeeQhuWtuOmZoi3nrqHnkIbnoJTnqbbmiYAc566h55CG5a246ZmiLeizh+ioiueuoeeQhuezuzHnsr7oqqDmpq7orb3lrbjpmaIt5oeJ55So56eR5oqA5a245aOr5a245L2N5a2456iLMeeyvuiqoOamruitveWtuOmZoi3phqvlrbjlt6XnqIvlrbjlo6vlrbjkvY3lrbjnqIso57K+6Kqg5qau6K295a246ZmiLemGq+WtuOW3peeoi+eglOeptuaJgDHnsr7oqqDmpq7orb3lrbjpmaIt6Imy5b2p6IiH54Wn5piO56eR5oqA56CU56m25omAQOeyvuiqoOamruitveWtuOmZoi3oibLlvanlvbHlg4/oiIfnhafmmI7np5HmioDlrbjlo6vlrbjkvY3lrbjnqIso57K+6Kqg5qau6K295a246ZmiLeaHieeUqOenkeaKgOeglOeptuaJgCXnsr7oqqDmpq7orb3lrbjpmaIt5LiN5YiG57O75a245aOr54+tOueyvuiqoOamruitveWtuOmZoi3mh4nnlKjnp5HmioDnoJTnqbbmiYDmnZDmlpnnp5HmioDlrbjnqIso5bel56iL5a246ZmiLeiHquWLleWMluWPiuaOp+WItueglOeptuaJgBzlt6XnqIvlrbjpmaIt5bel56iL5a245aOr54+tHOW3peeoi+WtuOmZoi3ljJblrbjlt6XnqIvns7sc5bel56iL5a246ZmiLeeHn+W7uuW3peeoi+ezuzflt6XnqIvlrbjpmaIt57ag6IO955Si5qWt5qmf6Zu75bel56iL5a245aOr5a245L2N5a2456iLHOW3peeoi+WtuOmZoi3mqZ/morDlt6XnqIvns7si5bel56iL5a246ZmiLeadkOaWmeenkeaKgOeglOeptuaJgBzlt6XnqIvlrbjpmaIt6Leo57O75omA5a2456iLOuW3peeoi+WtuOmZoi3lt6XnqIvmioDooZPnoJTnqbbmiYDmioDogbflsIjmpa3nmbzlsZXlrbjnqIsl5bel56iL5a246ZmiLeadkOaWmeenkeWtuOiIh+W3peeoi+ezuwNBTEwVMAROVUxMBDQtQUQENC1DRAQ0LURFBDQtRFQENC1EWAQyLUNTBDItRUMEMi1FRQQyLUVPBDItRVQENS1DQwQ1LUVQBDUtRkwENS1HRQQ1LVBFBDUtVkUENi1JQgQ2LVBBBDYtVEIENi1UTQQzLUJBBDMtRkIEMy1GTgQzLUlNBDMtTUEEMy1NQgQzLU1HBDMtTUkEMC1BVAQwLUJCBDAtQkUEMC1DSQQwLUNYBDAtRU4EMC1IQwQwLU1TBDEtQUMEMS1DRQQxLUNIBDEtQ1QEMS1HWAQxLU1FBDEtTVMEMS1SUwQxLVRWBDEtVFgBXxQrAzBnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2cWAWZkAgEPZBYCZg8QDxYIHwEFCUVkdV9Pcmdhbh8CBQZQZXJpb2QfAwUCTk8fBGdkEBUBCeS4jemBuOaThxUBBE5VTEwUKwMBZxYBZmQCAg9kFgJmDxAPFggfAQUJRWR1X09yZ2FuHwIFBWdyYWRlHwMFAk5PHwRnZBAVAQnkuI3pgbjmk4cVAQROVUxMFCsDAWcWAWZkAgMPZBYCZg8QDxYIHwEFCUVkdV9Pcmdhbh8CBQVDbGFzcx8DBQJOTx8EZ2QQFQEJ5LiN6YG45pOHFQEETlVMTBQrAwFnFgFmZAIED2QWCGYPZBYCZg8QDxYIHwEFCUVkdV9Pcmdhbh8CBRFDb2xsZWdlRGVwYXJ0bWVudB8DBQJOTx8EZ2QQFTAJ5LiN6YG45pOHFuioreioiOWtuOmZoi3lu7rnr4nns7si6Kit6KiI5a246ZmiLeWJteaEj+ioreioiOWtuOWjq+ePrRzoqK3oqIjlrbjpmaIt6Kit6KiI56CU56m25omAH+ioreioiOWtuOmZoi3lt6XllYbmpa3oqK3oqIjns7sr6Kit6KiI5a246ZmiLeWJteaEj+ioreioiOWtuOWjq+WtuOS9jeWtuOeoixzpm7vos4flrbjpmaIt6LOH6KiK5bel56iL57O7HOmbu+izh+WtuOmZoi3pm7vos4flrbjlo6vnj60c6Zu76LOH5a246ZmiLembu+apn+W3peeoi+ezuyLpm7vos4flrbjpmaIt5YWJ6Zu75bel56iL56CU56m25omAHOmbu+izh+WtuOmZoi3pm7vlrZDlt6XnqIvns7sl5Lq65paH56S+5pyD5a246ZmiLeS6uuaWh+ekvuacg+WtuOenkSXkurrmlofnpL7mnIPlrbjpmaIt5bir6LOH5Z+56IKy5Lit5b+DIuS6uuaWh+ekvuacg+WtuOmZoi3mh4nnlKjlpJboqp7ns7sf5Lq65paH56S+5pyD5a246ZmiLemAmuitmOWtuOenkRzkurrmlofnpL7mnIPlrbjpmaIt6auU6IKy5a6kMeS6uuaWh+ekvuacg+WtuOmZoi3mlbjkvY3lrbjnv5LoiIfmlZnogrLnoJTnqbbmiYA05pm65oWn6LKh55Si5a246ZmiLeaZuuaFp+iyoeeUouasiuWtuOWjq+WtuOS9jeWtuOeoiyLmmbrmhafosqHnlKLlrbjpmaIt5bCI5Yip56CU56m25omAMeaZuuaFp+iyoeeUouWtuOmZoi3np5HmioDnrqHnkIblrbjlo6vlrbjkvY3lrbjnqIso5pm65oWn6LKh55Si5a246ZmiLeenkeaKgOeuoeeQhueglOeptuaJgBznrqHnkIblrbjpmaIt5LyB5qWt566h55CG57O7K+euoeeQhuWtuOmZoi3osqHli5nph5Hono3lrbjlo6vlrbjkvY3lrbjnqIsi566h55CG5a246ZmiLeiyoeWLmemHkeiejeeglOeptuaJgBznrqHnkIblrbjpmaIt5bel5qWt566h55CG57O7EOeuoeeQhuWtuOmZoi1NQkEc566h55CG5a246ZmiLeeuoeeQhuWtuOWjq+ePrRznrqHnkIblrbjpmaIt566h55CG56CU56m25omAHOeuoeeQhuWtuOmZoi3os4foqIrnrqHnkIbns7sx57K+6Kqg5qau6K295a246ZmiLeaHieeUqOenkeaKgOWtuOWjq+WtuOS9jeWtuOeoizHnsr7oqqDmpq7orb3lrbjpmaIt6Yar5a245bel56iL5a245aOr5a245L2N5a2456iLKOeyvuiqoOamruitveWtuOmZoi3phqvlrbjlt6XnqIvnoJTnqbbmiYAx57K+6Kqg5qau6K295a246ZmiLeiJsuW9qeiIh+eFp+aYjuenkeaKgOeglOeptuaJgEDnsr7oqqDmpq7orb3lrbjpmaIt6Imy5b2p5b2x5YOP6IiH54Wn5piO56eR5oqA5a245aOr5a245L2N5a2456iLKOeyvuiqoOamruitveWtuOmZoi3mh4nnlKjnp5HmioDnoJTnqbbmiYAl57K+6Kqg5qau6K295a246ZmiLeS4jeWIhuezu+WtuOWjq+ePrTrnsr7oqqDmpq7orb3lrbjpmaIt5oeJ55So56eR5oqA56CU56m25omA5p2Q5paZ56eR5oqA5a2456iLKOW3peeoi+WtuOmZoi3oh6rli5XljJblj4rmjqfliLbnoJTnqbbmiYAc5bel56iL5a246ZmiLeW3peeoi+WtuOWjq+ePrRzlt6XnqIvlrbjpmaIt5YyW5a245bel56iL57O7HOW3peeoi+WtuOmZoi3nh5/lu7rlt6XnqIvns7s35bel56iL5a246ZmiLee2oOiDveeUoualreapn+mbu+W3peeoi+WtuOWjq+WtuOS9jeWtuOeoixzlt6XnqIvlrbjpmaIt5qmf5qKw5bel56iL57O7IuW3peeoi+WtuOmZoi3mnZDmlpnnp5HmioDnoJTnqbbmiYAc5bel56iL5a246ZmiLei3qOezu+aJgOWtuOeoizrlt6XnqIvlrbjpmaIt5bel56iL5oqA6KGT56CU56m25omA5oqA6IG35bCI5qWt55m85bGV5a2456iLJeW3peeoi+WtuOmZoi3mnZDmlpnnp5HlrbjoiIflt6XnqIvns7sDQUxMFTAETlVMTAQ0LUFEBDQtQ0QENC1ERQQ0LURUBDQtRFgEMi1DUwQyLUVDBDItRUUEMi1FTwQyLUVUBDUtQ0MENS1FUAQ1LUZMBDUtR0UENS1QRQQ1LVZFBDYtSUIENi1QQQQ2LVRCBDYtVE0EMy1CQQQzLUZCBDMtRk4EMy1JTQQzLU1BBDMtTUIEMy1NRwQzLU1JBDAtQVQEMC1CQgQwLUJFBDAtQ0kEMC1DWAQwLUVOBDAtSEMEMC1NUwQxLUFDBDEtQ0UEMS1DSAQxLUNUBDEtR1gEMS1NRQQxLU1TBDEtUlMEMS1UVgQxLVRYAV8UKwMwZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnZ2dnFgFmZAIBD2QWAmYPEA8WCB8BBQlFZHVfT3JnYW4fAgUGUGVyaW9kHwMFAk5PHwRnZBAVAQnkuI3pgbjmk4cVAQROVUxMFCsDAWcWAWZkAgIPZBYCZg8QDxYIHwEFCUVkdV9Pcmdhbh8CBQVncmFkZR8DBQJOTx8EZ2QQFQEJ5LiN6YG45pOHFQEETlVMTBQrAwFnFgFmZAIDD2QWAmYPEA8WCB8BBQlFZHVfT3JnYW4fAgUFQ2xhc3MfAwUCTk8fBGdkEBUBCeS4jemBuOaThxUBBE5VTEwUKwMBZxYBZmQCAQ9kFgJmD2QWAmYPZBYCAgEPZBYCZg9kFgJmDw8WAh8AaGRkAiUPZBYCZg9kFgJmD2QWBAIBDxBkZBYAZAIDDxBkZBYAZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WBwUHQWNiMDEwMQUHQWNiNjEwMQUHQWNiNjEwMgUHQkNIMDEwMQUJQ2hlY2tib3gzBQpDaGVja2JveF9HBQpDaGVja2JveF9DdnTv81x9dcCSGgcXhBFunnLoZBI=",
        "__EVENTVALIDATION" => "/wEWLwLrptb1AgLM9PumDwKjhumiAwLv8KKrDwKNtjkC4OH6vwYCrNq3uAICk8zFmAMCrJreugoCqJnyjgwC+/+yGgLCnqHTDALXy76QCwLPjbGPDAK2ps2PAwK708rUCQKVy/vIAwKj1M+0CwKwgc/xAwK6irmeAwKrzMWwBgK4+cWFAwK6jqJ/AqLQ1d0MAreNtbkJAtaEopQIAsWN1OIBAsq6tM4OAqDK62YCiIH+pQ0Cnb7egQoCpIDuuw0C+pWJ6g4C/6CC2AcC1KyV7QUC/6CK1woCmoqo7AQCjtvS2ggCqP6QtQwCvaC+6AcCuqC+6AcCu6C+6AcCgOTX3AkClOPj3gkClOPT3gkC2JvqjAsCxaLpxghScEPMf/xN8jojOjHndyzKWphPqA==",
        "semester_list" => "1022 (一百零二學年度第二學期)",
        "Acb0101" => "on",
        "BCH0101" => "on",
        "Ctb0101" => $course_no, //查詢條件中的課程代碼
        "Ctb0201" => "",
        "Ctb0301" => "",
        "QuerySend" => "送出查詢",
    );

    //開始計時
    $time_start = microtime(true);
    //初始化CURL
    $ch = curl_init();
    //設定CURL的選項，一開始先設定課程查詢的網址，連線方法為POST
    $options = array(
        CURLOPT_NOBODY => true,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_REFERER => $queryURL,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_URL => $queryURL,
        CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 6.1; WOW64)",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($post),
    );
    curl_setopt_array($ch, $options);
    //curl_setopt($ch, CURLINFO_HEADER_OUT, true); //request header
    //curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    //執行CURL，目的為取得cookie
    $result = curl_exec($ch);
    //檢查是否timeout
    if ($result === false) {
        curl_close($ch);
        return '連線逾時';
    }
    //根據傳回的結果去拆解出cookie
    $str = array();
    if (preg_match('/Set-Cookie:(.*);/iU', $result, $str)) {
        $cookie = trim($str[1]);
    }
    //設定新的cookie
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);

    //DEBUG
    if (isset($DEBUG)) {
        echo "<pre>\n";
        //print_r("curl_getinfo:\n" . curl_getinfo($ch, CURLINFO_HEADER_OUT));
        echo "Cookie: $cookie\r\n";
        echo 'time:' . (microtime(true) - $time_start);
        echo "</pre>\n";
        //print_r($result);  //DEBUG
    }

    //將連線方法改回GET
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    //設定課程網址
    curl_setopt($ch, CURLOPT_URL, $resultURL);
    //執行CURL不接收結果，目的是為了讓SERVER知道我們在看查詢結果，如果沒這一步則會錯誤
    curl_exec($ch);

    //輸出網頁中BODY
    curl_setopt($ch, CURLOPT_NOBODY, false);
    //設定課程網址
    curl_setopt($ch, CURLOPT_URL, $courseURL);
    //執行CURL，取得目標課程的相關資料
    $result = curl_exec($ch);
    //關閉CURL
    curl_close($ch);
    //停止計時
    $time_end = microtime(true);
    //計算花費的時間
    $time = $time_end - $time_start;

    //分割字串，先判斷有沒有取得結果
    $tmp = explode('dlHead_ctl01_DetailHeader', $result);
    if (count($tmp) === 2) {
        //簡化取得的結果
        $result = $tmp[1];
        //DEBUG
        if (isset($DEBUG)) {
            print_r($result);  //DEBUG
        }
        //預設值
        $course_name = '';
        $numbet_diff = 0;
        //取得課程名稱
        if (preg_match('/課程名稱.+<td>(.+)<\/td>/', $result, $str)) {
            ///課程名稱<\/td><td>(.+)<\/font>/
            $course_name = trim($str[1]);
            //取得目前選課人數
            if (preg_match('/合班選課人數：(\d+)　/', $result, $str)) {
                $number_now = $str[1];
            } else if (preg_match('/目前選課人數.+<td>(\d+)</', $result, $str)) {
                $number_now = $str[1];
            } else {
                $number_now = '無法取得目前選課人數';
            }
            //取得加退選課人數上限
            if (preg_match('/加退選課人數上限.+<td>(\d+)</', $result, $str)) {
                $number_max = $str[1];
                $numbet_diff = $number_max - $number_now;
            } else {
                $numbet_diff = '無法取得選課人數上限:';
            }
        } else {
            $course_name = '無法取得課程名稱';
        }
        $output = "{\"name\": \"$course_name\", \"number\": \"$numbet_diff\", \"time\": \"$time\"}";
    } else {
        $output = '無法取得結果';
    }
    return $output;
}

/**
 * 產生一組COOKIE
 * @return string COOKIE
 */
function getCookie() {
    $rand_str = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $cookie = 'ASP.NET_SessionId=';
    for ($i = 0; $i < 24; $i++) {
        $cookie .= substr($rand_str, mt_rand(0, 35), 1);
    }
    return $cookie;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>查詢選課人數的餘額</title>
        <!-- Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <!-- JavaScript plugins (requires jQuery) -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.min.js"></script>
        <style>
            .form-inline input[type="text"] {
                width: 180px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1">
                    <div class="text-center">
                        <h1>查詢選課人數的餘額
                            <span class="btn-group text-left">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    分流
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width: 80px;">
                                    <li><a href="http://mild88003.lionfree.net/CourseVacancy.php">分流1</a></li>
                                    <li><a href="http://mild88004.lionfree.net/CourseVacancy.php">分流2</a></li>
                                    <li><a href="http://mild88005.lionfree.net/CourseVacancy.php">分流3</a></li>
                                    <li><a href="http://mild88006.lionfree.net/CourseVacancy.php">分流4</a></li>
                                    <li><a href="http://mild88007.lionfree.net/CourseVacancy.php">分流5</a></li>
                                </ul>
                            </span>
                        </h1>
                        <p>
                            本服務可以根據您所輸入的課程代碼，查詢該課程的選課人數餘額，餘額之計算方法為<i><u>加退選課人數上限-目前選課人數</u></i>，餘額為0者將會自動定時查詢，直到餘額大於0為止。
                            請注意！本服務僅限台灣科技大學學生使用，切勿用於非法用途，如因為使用本服務而造成任何損失，請自行承擔。
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <form class="form text-center" id="form">
                        <div class="form-group" style="">
                            <input type="text" class="form-control" style="max-width: 12em; display: inline-block;" name="no" id="course_no" maxlength="9" placeholder="課程代碼">
                            <button type="submit" class="btn btn-primary" id="submit">查詢</button>
                            <a href="#" id="showSetting"><span class="badge"><span class="glyphicon glyphicon-cog"></span></span></a>
                        </div>
                        <div class="form-group" id="setting">
                            <label for="timeInterval">定時查詢的間隔
                                <input type="number" class="form-control" style="max-width: 9em; display: inline-block;" id="timeInterval" min="1000" max="2147483647" step="1000" value="8000"> 毫秒
                            </label>
                            <br>
                            <label class="checkbox-inline">
                                <input type="checkbox" id="soundAlert" value="option1" checked> 開啟聲音提醒
                            </label>
                        </div>
                    </form>
                    <div class="alert alert-danger">
                        <button type="button" class="close" onclick="$(this).parent().fadeToggle();">&times;</button>
                        <div id="alert_msg"></div>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="col-12">
                    <hr>
                    <div id="result">
                        <table class="table table-hover table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th class="hidden-xs">
                                        課程代碼
                                    </th>
                                    <th>
                                        課程名稱
                                    </th>
                                    <th>
                                        餘額
                                    </th>
                                    <th class="hidden-xs">
                                        花費時間
                                    </th>
                                    <th class="hidden-xs">
                                        最後查詢
                                    </th>
                                    <th style="width: 2em;">
                                    </th>
                                    <th style="width: 2em;">
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="hide" id="footer"> 
                    <audio id="player" controls preload="auto">
                        <source src="https://dl.dropboxusercontent.com/u/41064483/ding.ogg" type="audio/ogg">
                        <source src="https://dl.dropboxusercontent.com/u/41064483/ding.mp3" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                    <a href="http://SiteStates.com" title="站長工具" target="_blank">
                        <img src="http://SiteStates.com/show/image/28143.jpg" border="0">
                    </a>
                </div>
            </div>
        </div>

        <script>
            //查詢選課人數的餘額，參數course_no為課程代碼
            function query(course_no) {
                //如果course_no沒有查詢過，就產生新的資料列
                if ($("#" + course_no).length === 0) {
                    //產生新的資料列
                    $('tbody')
                            .append(
                                    $('<tr>').attr('id', course_no).append(
                                    $('<td>').attr('id', 'no_' + course_no).attr('class', 'hidden-xs').html(course_no))
                                    .append(
                                            $('<td>').attr('id', 'name_' + course_no))
                                    .append(
                                            $('<td>').attr('id', 'number_' + course_no))
                                    .append(
                                            $('<td>').attr('id', 'time_' + course_no).attr('class', 'hidden-xs'))
                                    .append(
                                            $('<td>').attr('id', 'last_' + course_no).attr('class', 'hidden-xs'))
                                    .append(
                                            $('<td>').attr('id', 'refresh_' + course_no))
                                    .append(
                                            $('<td>').attr('id', 'remove_' + course_no))
                                    );
                    //重新整理, 移除的按鈕
                    $('#refresh_' + course_no)
                            .append(
                                    $('<a>').val(course_no).attr('class', 'refresh').append(
                                    $('<span>').attr('class', 'glyphicon glyphicon-refresh'))
                                    );
                    $('#remove_' + course_no)
                            .append(
                                    $('<a>').val(course_no).attr('class', 'remove').append(
                                    $('<span>').attr('class', 'glyphicon glyphicon-remove'))
                                    );
                }

                //更新資料
                $.ajax({
                    //url: '<?php echo $_SERVER['SCRIPT_NAME']; ?>',
                    type: 'get',
                    cache: false,
                    data: {no: course_no},
                    dateType: 'json',
                    course_no: course_no,
                    error: function(xhr) {
                        //console.log(xhr);
                    },
                    success: function(data) {
                        try {
                            //console.log(data);
                            var no = $(this)[0]['course_no'];
                            var obj = $.parseJSON(data);
                            $('#name_' + no).html(obj.name);
                            $('#number_' + no).html(obj.number);
                            $('#time_' + no).html(obj.time);
                            $('#last_' + no).html(new Date().toLocaleString());
                            //餘額大於0就用綠色，否則用黃色
                            if (obj.number > 0) {
                                //有開啟聲音提醒就播放
                                if ($('#soundAlert').prop("checked")) {
                                    $('#player').trigger('play');
                                }
                                $('#' + no).attr('class', 'success');
                            } else {
                                $('#' + no).attr('class', 'warning');
                            }
                        }
                        catch (e) {
                            //console.log(e);
                            var no = $(this)[0]['course_no'];
                            $('#number_' + no).html(data);
                            $('#' + no).attr('class', 'danger');
                            //如果目前的課程名稱為空，表示發生錯誤的原因可能是找不到此課程，所以就要移除此資料列
                            if ($('#name_' + no).html() === '') {
                                //錯誤的資料列消失並移除
                                $('#' + no).fadeOut(2000, function() {
                                    $(this).remove();
                                });
                            }

                        }
                    }
                });
            }

            //隱藏設定介面、警告訊息
            $("#setting").hide();
            $('.alert').hide();
            //修改form的submit事件，改成button.clock
            $('#form').on('submit', function() {
                $('#submit').click();
                return false;
            });
            //按下設定按鈕
            $('#showSetting').on('click', function() {
                $("#setting").toggle();
            });
            //按下查詢按鈕
            $('#submit').on('click', function() {
                var course_no = $("#course_no").val().trim();
                if (course_no === "") {
                    return; //沒有輸入課程代碼
                }
                else if (/^[A-Z]{2}\w\d{6}$/.test(course_no)) {
                    $('.alert').hide();
                    $("#course_no").val('');
                    query(course_no);
                } else {
                    $('#alert_msg').html("您所輸入的課程代碼格式錯誤！");
                    $('.alert').fadeIn();
                }
            });
            //設定新的時間間隔
            $('#timeInterval').on('change', function() {
                var n = parseInt($('#timeInterval').val());
                if (n) {
                    modifyInterval(parseInt($('#timeInterval').val()));
                }
            });
            //設定委派給每個refresh, remove的按鈕
            $("#tbody").delegate(".refresh", "click", function() {
                query($(this).val());
            }).delegate(".remove", "click", function() {
                $(this).parent().parent().fadeOut(500, function() {
                    $(this).remove();
                });
            });
            //定時查詢
            function autoQuery() {
                $('#tbody').children().each(function() {
                    var course_no = $(this).attr('id');
                    if ($('#number_' + course_no).html() <= 0) {
                        query(course_no);
                    }
                });
            }
            //預設的時間間隔
            var timeInterval = 8000;
            var auto = setInterval(autoQuery, timeInterval);
            //修改定時查詢的時間
            function modifyInterval(interval) {
                timeInterval = interval;
                clearInterval(auto);
                auto = setInterval(autoQuery, timeInterval);
            }
        </script>
    </body>
</html>