
const displayModal = (title, body, footer='', color='bg-indigo-500') => {
    const modal = $(`#modal`)[0]
    const titleContainer = $('#modal-title').closest('div')

    $(titleContainer).removeClass('bg-indigo-500 bg-red-500').addClass(color);

    $(`#modal-title`).text(title)
    $(`#modal-content`).html(body)
    $(`#modal-close-btn`).before(footer)

    modal.showModal()
}

const closeModal = () => {
    const modal = $(`#modal`)[0]
    const closeBtn = $(`#modal-close-btn`)

    $(`#modal-title`).text('')
    $(`#modal-content`).html('')
    $(`#modal-footer`).html(closeBtn)
    modal.close()
}

const getInputValues = (...inputNames) => {
    let values = []

    for (const name of inputNames) {
        const node = $(`input[name=${name}]`)
        if (node) {
            values.push($(node).val())
        }
    }

    return values
}

const resetInputField = (name, value="") => {
    $(`input[name="${name}"]`).val(value)
}

const parseErrorMessages = (validationErrors) => {
    let content = ''

    const messages = Object.values(validationErrors).flat()
    messages.map(err => content += `<li>${err}</li>`)

    return `<ul>${content}</ul>`
}

const regeneratePaginationLinks = (links) => {
    clearPaginationLinks()

    links.forEach(link => {
        let btnText = link.label.replace("&laquo;", "").replace("&raquo;", "")
        const btn = $("<button>")
            .addClass("text-black p-1 border border-gray-500 min-w-fit px-4 rounded-lg m-2")
            .addClass(link.active ? "bg-gray-300" : "bg-white");

        if (link.url) {
            btn.addClass("page-btn").attr("url", link.url)
        }

        btn.text(btnText);
        $("#pages-container").append(btn)
    });
}

const clearPaginationLinks = () => {
    $('#pages-container button').remove()
}

const sortTableBy = (columnName, endpoint, after) => {
    let queryParams = new URLSearchParams(window.location.search)

    const orderBy = queryParams.get("sort_by")
    if (columnName !== orderBy) {
        queryParams.set("sort_by", columnName)
        queryParams.set("asc", "true")
    } else {
        const orderDirection = queryParams.get("asc")
        queryParams.set("asc", orderDirection === "false")
    }

    history.pushState(null, "", `${endpoint}?${queryParams.toString()}`)

    after()
}
