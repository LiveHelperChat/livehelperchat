
class _dummyHelper {
    constructor() {
        console.log('dummy helper');
    }

    testCall() {
        console.log('dynamically loaded v4');
    }
}

const dummyHelper = new _dummyHelper();
export { dummyHelper };