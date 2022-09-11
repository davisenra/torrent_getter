import { formatBytes } from "./functions.js";

const resultsBox = document.querySelector('.search__results');
const searchButton = document.querySelector('.search__button');
const searchString = document.querySelector('.search__box__input');
const loadingRipple = document.querySelector('.loading__ripple');

function populateResults(results) {
    if (results.error) {
        resultsBox.innerHTML = '<p>No results found!</p>';
        return;
    }

    let html = '<h5>' + (results.data.torrent_results).length + ' results were found</h5>';

    (results.data.torrent_results).forEach((item) => {
        html +=
            '<a href="' + item.download + '" ' +
            'class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">' +
            '<span>' + item.title + '</span>' +
            '<span class="badge badge-light badge-pill p-1">' +
                'Size: ' + formatBytes(item.size) +
                '/ S: ' + item.seeders +
                '/ L: ' + item.leechers +
            '</span>' +
            '</a>';
    });

    resultsBox.innerHTML = html;
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

        return torrents;
    } catch (e) {
        console.error(e)
    }
}

async function queryAction() {
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

queryAction();