export function page (path) {
    return ()=>{};
    //return () => page(/* webpackChunkName: '' */ `${path}`).then(m => m.default || m)
}
