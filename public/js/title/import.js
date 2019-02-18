/**
 * title/import.js
 */
var cinerino = window.cinerino;
/**
 * WARNING
 * API_ENDPOINTは各プロジェクトごとに変更する
 */
var API_ENDPOINT = 'https://toei-cinerino-api-development.azurewebsites.net';
var CLIENT_ID = '3acp6i998hvi810lcv2svq2lsh';
var CLIENT_SECRET = 'a4kp1rrqa2a4mve0o23h1339o37hjdtequtgpib3b82l690rvml';

/**
 * 認証情報取得
 * WARNING
 * サーバーサイドにAPIとして実装する
 * clientId,clientSecret,authorizationServerDomainは各プロジェクトごとに変更する
 */
function getCredentials() {
    var clientId = CLIENT_ID;
    var clientSecret = CLIENT_SECRET;
    var authorization = 'Basic ' + btoa(clientId + ':' + clientSecret);
    var authorizationServerDomain = 'toei-cinerino-development.auth.ap-northeast-1.amazoncognito.com';
    var options = {
        dataType: 'json',
        url: 'https://' + authorizationServerDomain + '/oauth2/token',
        type: 'POST',
        timeout: 10000,
        contentType: 'application/x-www-form-urlencoded',
        headers: {
            Authorization: authorization
        },
        data: {
            'grant_type': 'client_credentials'
        }
    };
    return $.ajax(options);
}

/**
 * 設定作成
 * @param {string} accessToken 
 */
function createOptions(accessToken) {
    const option = {
        domain: '',
        clientId: '',
        redirectUri: '',
        logoutUri: '',
        responseType: '',
        scope: '',
        state: '',
        nonce: null,
        tokenIssuer: ''
    };
    var auth = cinerino.createAuthInstance(option);
    auth.setCredentials({ accessToken: accessToken });
    return {
        endpoint: API_ENDPOINT,
        auth: auth
    }
}
/**
 * 作品一覧取得
 */
function getMovies(from, through) {
    showLoader();
    $('.search-result').hide();
    var days = ['日', '月', '火', '水', '木', '金', '土'];
    getCredentials().then(function (credentials) {
        var accessToken = credentials.access_token;
        var options = createOptions(accessToken);
        var creativeWorkService = new cinerino.service.CreativeWork(options);
        var searchConditions = {};
        if (from.length > 0) {
            searchConditions.datePublishedFrom = from;
        }
        if (through.length > 0) {
            searchConditions.datePublishedThrough = through;
        }

        return creativeWorkService.searchMovies(searchConditions);
    }).then(function (movies) {
        // console.log(movies);
        if (movies.totalCount > 0) {
            var ids = movies.data.map(function(m) {
                return m.identifier
            });
            api.title.findImported(ids)
                .done(function(res) {
                    console.log(res);
                    var doms = [];
                    movies.data.forEach(function (movie) {
                        if (typeof movie.datePublished === 'string') {
                            var datePublished = movie.datePublished.split('-').join('/').slice(0, 10);
                            datePublished += ' (' + days[new Date(movie.datePublished).getDay()] + ')';
                        } else {
                            var datePublished = '';
                        }
                        var isImported = res.data.indexOf(movie.identifier) >= 0;
                        var dom = '\
                        <tr class="' + (isImported ? 'imported' : 'unimported') + '">\
                            <td><input type="checkbox"' + (!isImported ? '' : ' disabled') + '></td>\
                            <td>' + datePublished + '</td>\
                            <td'+ (!isImported ? ' style="color: red"' : '') +'>' + (!isImported ? '未反映' : '反映済') + '</td>\
                            <td>' + movie.identifier + '</td>\
                            <td>' + movie.name + '</td>\
                            <td style="display: none">' + movie.headline + '</td>\
                            <td style="display: none">' + movie.contentRating + '</td>\
                        </tr>';
                        doms.push(dom);
                    });
                    $('.search-result table tbody').html(doms.join('\n'));
                    $('.search-result table tbody tr.unimported').click(function(event) {
                        if (event.target.nodeName !== 'INPUT') {
                            var checkBoxes = $(this).find('input[type=checkbox]');
                            checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                        }
                    });
                    $('.search-result').show();
                })
                .fail(function(err) {
                    console.error(err);
                    $('.alert').html('エラーが発生しました。').addClass('alert-danger').removeClass('alert-info').show();
                    window.scrollTo(0, 0);
                    $('.search-result').hide();
                })
                .always(function() {
                    hideLoader();
                });
        } else {
            hideLoader();
            $('.alert').html('検索結果は０件です。').addClass('alert-info').removeClass('alert-danger').show();
            window.scrollTo(0, 0);
            $('.search-result').hide();
        }
    }).catch(function (error) {
        hideLoader();
        console.error(error);
        $('.alert').html('作品一覧取得エラー。').addClass('alert-danger').removeClass('alert-info').show();
        window.scrollTo(0, 0);
    });
}

function importTitles() {
    var data = [];
    $('.search-result table tr td input:checked').each(function() {
        var td = $(this).parent();
        var title = {};
        // 公開予定日
        if (td.next().html().length > 0) {
            title.publishing_expected_date = td.next().html().slice(0, 10);
        } else {
            title.not_exist_publishing_expected_date = 1;
        }
        title.chever_code = td.next().next().next().html();
        title.name = td.next().next().next().next().html();
        title.sub_title = td.next().next().next().next().next().html();
        title.rating = td.next().next().next().next().next().next().html();
        data.push(title);
    });
    // console.log(data);
    showLoader();
    $('.alert').hide();
    api.title.importTitles(data)
        .done(function(res) {
            // console.log(res);
            if (res.status === 'success') {
                $('.alert').html('取り込み成功です。').addClass('alert-info').removeClass('alert-danger').show();
            } else {
                if (res.errors.length === data.length) {
                    var message = '取り込み失敗です。'
                } else {
                    var message = '作品マスタ取り込みは' + data.length + '件の中' +
                        (data.length - res.errors.length) + '件が取り込み成功、' +
                        (res.errors.length) + '件が取り込み失敗です。';
                }
                $('.alert').html(message).addClass('alert-danger').removeClass('alert-info').show();
            }
            getMovies(
                $('[name=public_start_dt]').val(),
                $('[name=public_end_dt]').val()
            );
            window.scrollTo(0, 0);
        })
        .fail(function(err) {
            console.error(err);
            $('.alert').html('エラーが発生しました。').addClass('alert-danger').removeClass('alert-info').show();
            window.scrollTo(0, 0);
            hideLoader();
        });
}

$(function(){
    var $form = $('form[name="cinerino_title_find"]');
    
    $form.find('.datetimepicker').datetimepicker(datepickerOption);
    $form.find('button').click(function() {
        $('.alert').hide();
        getMovies(
            $('[name=public_start_dt]').val(),
            $('[name=public_end_dt]').val()
        );
    })
});
