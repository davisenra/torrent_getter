import { formatBytes } from "./functions.js";

const resultsBox = document.querySelector('.search__results');
const searchButton = document.querySelector('.search__button');
const searchString = document.querySelector('.search__box__input');
const loadingRipple = document.querySelector('.loading__ripple');

queryAction();

function queryAction() {
    searchButton.addEventListener('click', async (e) => {
        let data = {
            "search-string": searchString.value
        };
        searchString.value = ''
        resultsBox.innerHTML = ''
        loadingRipple.classList.toggle('loading__visible');

        let torrents = await fetchTorrents(data);

        loadingRipple.classList.toggle('loading__visible');
        populateResults(torrents);
    })
}

async function fetchTorrents(data) {
    let torrents;

    try {
        const response = await fetch('/search', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        })

        torrents = await response.json();

        console.log(torrents)

        return torrents;
    } catch (e) {
        console.error(e)
    }
}

function populateResults(results) {
    if (results.length === 0) {
        resultsBox.innerHTML = '<p>No results found!</p>';
        return;
    }

    let html = '<h5>' + (results.data).length + ' results were found</h5>';

    (results.data).forEach((item) => {
        if (item.source === 'YTS.MX') {
            html +=
                '<a href="' + item.download + '" ' + 'class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">' +
                    '<div class="info">' +
                        '<span class="badge badge-danger badge-pill p-1 mr-2">' + item.source + '</span>' +
                        '<span>' + item.title + '</span>' +
                    '</div>'
        } else if (item.source === 'RARBG') {
            html +=
                '<a href="' + item.download + '" ' + 'class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">' +
                    '<div class="info">' +
                        '<span class="badge badge-success badge-pill p-1 mr-2">' + item.source + '</span>' +
                        '<span>' + item.title + '</span>' +
                    '</div>'
        }

        html +=
                '<div class="actions">' +
                    '<span class="badge badge-light badge-pill p-1">' + 'Size: ' + formatBytes(item.size) + '</span>' +
                    '<span class="badge badge-light badge-pill p-1">' + 'S: ' + item.seeders + '</span>' +
                    '<span class="badge badge-light badge-pill p-1">' + 'L: ' + item.leechers + '</span>' +
                '</div> </a>';
    });

    resultsBox.innerHTML = html;
}