let utils = {

}

utils.bytesToSize = (bytes) => {
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    if (bytes == 0) return '0 Byte';
    let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
    return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

utils.milliSecondsToSeconds = (ms , suffix = " sec") => {
    return (ms/1000).toFixed(2) + suffix;
}

export default utils