let sqlQueries = "";

function readRow(row) {
    const hyperLink = row.querySelector("a.link_wXY7O");

    const id        = hyperLink.href;
    const image_url = (hyperLink.querySelector('img') || {src: ''}).src;
    const title     = extractTextRecursively(row.querySelector('td.title_K9_iv'));
    const post_year = row.querySelector('td.year_o3FNi').innerText;

    // console.log(`${id} - ${image_url} - ${title} - ${post_year}`);
    
    return {jsonRow: null, sqlQuery: `INSERT INTO \`feeds\` (\`type\`, \`timestamp\`, \`image_url\`, \`title\`, \`post_year\`, \`discogs_id\`) VALUES ('discografy', ${Math.floor(Date.now()/1000)}, '${image_url}', '${title.replace('\'', '\'\'')}', ${post_year}, '${id}')`};
}

function extractTextRecursively(node) {
  if (!node) return '';
  
  let text = '';

  if (node.nodeType === Node.TEXT_NODE) {
    return node.textContent;
  }

  if (node.nodeType === Node.ELEMENT_NODE) {
    if (node.classList.contains('formatsContainer_V59Zc')) return '';
    if (node.classList.contains('versionsButton_JJ96Y')) return '';

    for (let child of node.childNodes) {
      text += extractTextRecursively(child);
    }
  }

  return text;
}

function readRows(tbody) {
    const trs = tbody.querySelectorAll('tr');
    trs.forEach((tr) => {
        const data = readRow(tr);
        sqlQueries += data.sqlQuery + ';\n'; 
    });
}

const tbodies = document.querySelectorAll('table.releases_XFhKx tbody');
for (const tb of tbodies) {
    readRows(tb);
}