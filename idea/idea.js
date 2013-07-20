// ligaturise everything (kinda)
$.fn.ligature = function(str, lig) {
    return this.each(function() {
        this.innerHTML = this.innerHTML.replace(new RegExp(str, 'g'), lig);
    });
};
