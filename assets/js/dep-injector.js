((m) => {
    m.wkInjectDeps = (cls, obj) => {
        for (const k in obj)
            cls[k] = obj[k];
    };
})(window);