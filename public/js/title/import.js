/**
 * title/import.js
 */
var API_ENDPOINT;
var PROJECT_ID;
var TIMEZONE = 9; // JST Timezone

/**
 * 表示日付の修正
 */
function displayDateCorrection(date) {
    if (typeof date === 'string') {
        var d = new Date(date);
        d.setTime(d.getTime() + (TIMEZONE * 60 - d.getTimezoneOffset()) * 60 * 1000);
        return d.toISOString().slice(0, 10).split('-').join('/');
    } else {
        return '';
    }
}

/**
 * 作品一覧取得パラメター
 */
function getMovieParams() {
    var startFrom = $('[name=public_start_dt]').val();
    if (startFrom.length > 0) {
        startFrom = startFrom.split('/').join('-') + 'T00:00:00+09:00';
    }
    var endThrougth = $('[name=public_end_dt]').val();
    if (endThrougth.length > 0) {
        endThrougth = endThrougth.split('/').join('-') + 'T00:00:00+09:00';
    }
    return {
        startFrom: startFrom,
        endThrougth: endThrougth
    };
}
/**
 * 作品一覧取得
 */
function getMovies(from, through) {
    showLoader();
    $('.search-result').hide();
    var days = ['日', '月', '火', '水', '木', '金', '土'];
    api.auth.token()
        .then(function (res) {
            var accessToken = res.data.access_token;
            var params = {};
            if (from.length > 0) {
                params.datePublishedFrom = from;
            }
            if (through.length > 0) {
                params.datePublishedThrough = through;
            }
            var options = {
                dataType: 'json',
                url: API_ENDPOINT + '/projects/' + PROJECT_ID + '/creativeWorks/movie',
                type: 'GET',
                timeout: 30000,
                headers: {
                    Authorization: 'Bearer ' + accessToken
                },
                data: params,
            };

            return $.ajax(options);
        }).then(function (movies) {
            // console.log(movies);
            if (movies.length > 0) {
                var ids = movies.map(function (m) {
                    return m.identifier
                });
                api.title.findImported(ids)
                    .done(function (res) {
                        console.log(res);
                        var doms = [];
                        movies.forEach(function (movie) {
                            var isImported = res.data.indexOf(movie.identifier) >= 0;
                            var dom = '\
                        <tr class="' + (isImported ? 'imported' : 'unimported') + '">\
                            <td><input type="checkbox"' + (!isImported ? '' : ' disabled') + '></td>\
                            <td>' + displayDateCorrection(movie.datePublished) + '</td>\
                            <td'+ (!isImported ? ' style="color: red"' : '') + '>' + (!isImported ? '未反映' : '反映済') + '</td>\
                            <td>' + movie.identifier + '</td>\
                            <td>' + movie.name.ja + '</td>\
                            <td style="display: none">' + movie.headline + '</td>\
                            <td style="display: none">' + movie.contentRating + '</td>\
                        </tr>';
                            doms.push(dom);
                        });
                        $('.search-result table tbody').html(doms.join('\n'));
                        $('.search-result table tbody tr.unimported').click(function (event) {
                            if (event.target.nodeName !== 'INPUT') {
                                var checkBoxes = $(this).find('input[type=checkbox]');
                                checkBoxes.prop("checked", !checkBoxes.prop("checked"));
                            }
                        });
                        $('.search-result').show();
                    })
                    .fail(function (err) {
                        console.error(err);
                        $('.alert').html('エラーが発生しました。').addClass('alert-danger').removeClass('alert-info').show();
                        window.scrollTo(0, 0);
                        $('.search-result').hide();
                    })
                    .always(function () {
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
    $('.search-result table tr td input:checked').each(function () {
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
        .done(function (res) {
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
            var params = getMovieParams();
            getMovies(params.startFrom, params.endThrougth);
            window.scrollTo(0, 0);
        })
        .fail(function (err) {
            console.error(err);
            $('.alert').html('エラーが発生しました。').addClass('alert-danger').removeClass('alert-info').show();
            window.scrollTo(0, 0);
            hideLoader();
        });
}

$(function () {
    API_ENDPOINT = $('input[name=API_ENDPOINT]').val();
    PROJECT_ID = $('input[name=API_PROJECT_ID]').val();
    var $form = $('form[name="cinerino_title_find"]');
    $form.find('.datetimepicker').datetimepicker(datepickerOption);
    $form.find('button').click(function () {
        $('.alert').hide();
        var params = getMovieParams();
        getMovies(params.startFrom, params.endThrougth);
    })
});
