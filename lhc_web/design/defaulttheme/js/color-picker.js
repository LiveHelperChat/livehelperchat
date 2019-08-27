;(function() {
    /**
     *
     * @param {HTMLElement} node
     */
    const addClassName = (node, str) => {
        if (node.className.split(' ').filter(s => s === str).length === 0) {
            node.className += ` ${str}`
        }
    }
    /**
     *
     * @param {HTMLElement} node
     */
    const removeClassName = (node, str) => {
        node.className = node.className
            .split(' ')
            .filter(s => s !== str)
            .join(' ')
    }
    /**
     * è®¾å®šè¾¹ç•Œå€¼
     * @param {number} num
     * @param {number} max
     * @param {number} min
     */
    const numberBorder = (num, max, min) => Math.max(Math.min(num, max), min)

    /**
     * è½¬æ¢rgbé¢œè‰²åˆ°hsb
     * @param {string} hex
     */
    const rgbToHsb = hex => {
        const hsb = { h: 0, s: 0, b: 0 }
        if (hex.indexOf('#') === 0) {
            hex = hex.substring(1)
        }
        if (hex.length === 3) {
            hex = hex
                .split('')
                .map(s => s + s)
                .join('')
        }
        if (hex.length !== 6) return false
        hex = [hex.substr(0, 2), hex.substr(2, 2), hex.substr(4, 2)].map(s =>
            parseInt(s, 16)
        )
        const rgb = {
            r: hex[0],
            g: hex[1],
            b: hex[2]
        }
        const MAX = Math.max(...hex)
        const MIN = Math.min(...hex)
        //H start
        if (MAX === MIN) {
            hsb.h = 0
        } else if (MAX === rgb.r && rgb.g >= rgb.b) {
            hsb.h = (60 * (rgb.g - rgb.b)) / (MAX - MIN) + 0
        } else if (MAX === rgb.r && rgb.g < rgb.b) {
            hsb.h = (60 * (rgb.g - rgb.b)) / (MAX - MIN) + 360
        } else if (MAX === rgb.g) {
            hsb.h = (60 * (rgb.b - rgb.r)) / (MAX - MIN) + 120
        } else if (MAX === rgb.b) {
            hsb.h = (60 * (rgb.r - rgb.g)) / (MAX - MIN) + 240
        }
        //H end
        if (MAX === 0) {
            hsb.s = 0
        } else {
            hsb.s = 1 - MIN / MAX
        }
        hsb.b = MAX / 255
        return hsb
    }

    /**
     * ç”±ç™¾åˆ†æ¯”è½¬ä¸ºä¸€ä¸ªåŸºå‡†rgbé¢œè‰²
     * @param {number} heightPercent å½“å‰é€‰ä¸­ä½ç½®ç›¸å¯¹æ•´ä½“é«˜åº¦
     * @returns {r: any,g: any,b: any}
     */
    const heightToRgb = heightPercent => {
        heightPercent = 1 - heightPercent
        let rgb = { r: undefined, g: undefined, b: undefined }
        const percentInEach = heightPercent * 6
        return Object.entries(rgb).reduce(
            (lastObj, nowArr, index) =>
                Object.assign(lastObj, {
                    [nowArr[0]]: Math.floor(
                        (function() {
                            const left = ((index + 1) % 3) * 2
                            const right = left + 2
                            const differenceL = percentInEach - left
                            const differenceR = right - percentInEach
                            if (differenceL >= 0 && differenceR >= 0) {
                                return 0
                            }
                            const distance = Math.min(
                                Math.abs(differenceL),
                                Math.abs(differenceR),
                                Math.abs(6 - differenceL),
                                Math.abs(6 - differenceR)
                            )
                            return Math.min(255, 255 * distance)
                        })()
                    )
                }),
            {}
        )
    }

    const heightAddLAndT_ToRGB = (height, left, top) => {
        const rgb = heightToRgb(height)
        for (const key in rgb) {
            rgb[key] = (255 - rgb[key]) * (1 - left) + rgb[key]
            rgb[key] = rgb[key] * (1 - top)
        }
        return rgb
    }

    const rgbToHex = rgb => {
        const { r, g, b } = rgb
        return (
            Math.floor(r)
                .toString(16)
                .padStart(2, '0') +
            Math.floor(g)
                .toString(16)
                .padStart(2, '0') +
            Math.floor(b)
                .toString(16)
                .padStart(2, '0')
        )
    }

    /**
     *
     * @param {string} hex
     */
    const hexToRgb = hex => {
        return {
            r: parseInt(hex.substr(0, 2), 16),
            g: parseInt(hex.substr(2, 2), 16),
            b: parseInt(hex.substr(4, 2), 16)
        }
    }

    /**
     * æ— æ„ä¹‰çš„ç®€å†™ï¼Œé‡Œæœ‰çº¯ç²¹æ˜¯ä¸ºäº†æ–¹ä¾¿åŽ‹ç¼©ä»£ç 
     * @param {HTMLElement} str
     */
    const cE = str => document.createElement(str)

    class ColorPicker {
        constructor({ dom = cE('div'), input, value = 'FFF' } = {}) {
            this.dom = dom
            const thisClass = this

            // if (input) {
            //   input.addEventListener('click', e => {
            //     this.updateValue(input)
            //   })
            // }
            Array.prototype.forEach.call(this.getDOM().children, node => {
                node.remove()
            })
            addClassName(dom, 'color-picker')

            const rightBar = cE('div')
            rightBar.className = 'color-picker-right-bar'
            const rightBarPicker = cE('div')
            rightBarPicker.className = 'color-picker-right-bar-picker'

            rightBar.appendChild(rightBarPicker)

            const colorBlock = cE('div')
            colorBlock.className = 'color-picker-color-block'
            const gradientColor = cE('div')
            gradientColor.className =
                'color-picker-gradients color-picker-gradient-color'
            const gradientBlack = cE('div')
            gradientBlack.className =
                'color-picker-gradients color-picker-gradient-black'

            gradientColor.style.background =
                'linear-gradient(to right,#FFFFFF,#FF0000)'

            const gradientCircle = cE('div')
            gradientCircle.className = 'color-picker-circle'

            gradientBlack.appendChild(gradientCircle)

            const textInput = cE('input')
            const textInputBox = cE('div')
            textInputBox.className = 'color-picker-input'
            textInput.maxLength = 6
            textInput.style.width = '100%'
            textInput.style.height = '100%'
            textInputBox.appendChild(textInput)

            this.getDOM().appendChild(rightBar)
            this.getDOM().appendChild(colorBlock)
            this.getDOM().appendChild(textInputBox)
            this.getDOM().appendChild(gradientColor)
            this.getDOM().appendChild(gradientBlack)

            textInput.addEventListener('change', () => {
                this.setValue(textInput.value, true)
                this.onchange()
                this.updatePicker()
            })

            this.textInput = textInput
            this._gradientBlack = gradientBlack
            this._gradientColor = gradientColor
            this._rightBar = rightBar
            this._rightBarPicker = rightBarPicker
            this._colorBlock = colorBlock

            this._gradientCircle = gradientCircle

            this._height = 0
            this._mouseX = 0
            this._mouseY = 0

            this.setValue(value, true)
            this._lastValue = this.value
            this.updatePicker()
            //this.input = input

            const mouseMoveFun = e => {
                window.addEventListener('mouseup', function mouseUpFun() {
                    thisClass.getDOM().style.userSelect = 'text'
                    window.removeEventListener('mousemove', mouseMoveFun)
                    window.removeEventListener('mouseup', mouseUpFun)
                })
                const bbox = thisClass._gradientBlack.getBoundingClientRect()
                this._mouseX = e.clientX - bbox.left // * (p.width / bbox.width)
                this._mouseY = e.clientY - bbox.top // * (p.height / bbox.height)
                this.mouseBorder()
                this.setValue(
                    heightAddLAndT_ToRGB(this.height, this.position.x, this.position.y)
                )
                this.updatePicker()
            }
            const mouseMoveFunBar = e => {
                window.addEventListener('mouseup', function mouseUpFunBar() {
                    thisClass.getDOM().style.userSelect = 'text'
                    window.removeEventListener('mousemove', mouseMoveFunBar)
                    window.removeEventListener('mouseup', mouseUpFunBar)
                })
                const bbox = thisClass._rightBar.getBoundingClientRect()
                this._height = e.clientY - bbox.top // * (p.height / bbox.height)
                this.mouseBorderBar()
                this.setValue(
                    heightAddLAndT_ToRGB(this.height, this.position.x, this.position.y)
                )
                this.updatePicker()
            }
            this._gradientBlack.addEventListener('mousedown', e => {
                this.getDOM().style.userSelect = 'none'
                mouseMoveFun(e)
                window.addEventListener('mousemove', mouseMoveFun)
            })
            this._rightBar.addEventListener('mousedown', e => {
                this.getDOM().style.userSelect = 'none'
                mouseMoveFunBar(e)
                window.addEventListener('mousemove', mouseMoveFunBar)
            })

            if ('ontouchstart' in window) {
                const touchFun = e => {
                    e.preventDefault()
                    e = e.touches[0]
                    const bbox = thisClass._gradientBlack.getBoundingClientRect()
                    this._mouseX = e.clientX - bbox.left // * (p.width / bbox.width)
                    this._mouseY = e.clientY - bbox.top // * (p.height / bbox.height)
                    this.mouseBorder()
                    this.setValue(
                        heightAddLAndT_ToRGB(this.height, this.position.x, this.position.y)
                    )
                    this.updatePicker()
                }
                const touchFunBar = e => {
                    e.preventDefault()
                    e = e.touches[0]
                    const bbox = this._rightBar.getBoundingClientRect()
                    this._height = e.clientY - bbox.top // * (p.height / bbox.height)
                    this.mouseBorderBar()
                    this.setValue(
                        heightAddLAndT_ToRGB(this.height, this.position.x, this.position.y)
                    )
                    this.updatePicker()
                }
                this._gradientBlack.addEventListener('touchmove', touchFun)
                this._gradientBlack.addEventListener('touchstart', touchFun)
                this._rightBar.addEventListener('touchmove', touchFunBar)
                this._rightBar.addEventListener('touchstart', touchFunBar)
            }

            this._changeFunctions = []
            //this.updateValue()
        }
        onchange() {
            this._changeFunctions.forEach(fun =>
                fun({
                    target: this,
                    type: 'change',
                    timeStamp: performance.now()
                })
            )
        }
        /**
         *
         * @param {"change"} type
         * @param {function} fun
         */
        addEventListener(type, fun) {
            if (typeof fun !== 'function') {
                return
            }
            switch (type) {
                case 'change': {
                    this._changeFunctions.push(fun)
                    break
                }
            }
        }

        getValue(mode = 'value') {
            switch (mode) {
                case 'hex': {
                    return this._value
                }
                case 'rgb': {
                    return hexToRgb(this.getValue('hex'))
                }
                case 'hsb': {
                    return rgbToHsb(this.getValue('hex'))
                }
                case 'value':
                default: {
                    return '#' + this._value
                }
            }
        }
        getBrightness() {
            const { r, g, b } = this.getValue('rgb')
            return 0.299 * r + 0.587 * g + 0.114 * b
        }
        setValue(value, resetPosition = false) {
            let hex = ''
            switch (typeof value) {
                case 'string': {
                    if (value.indexOf('#') === 0) {
                        value = value.substring(1)
                    }
                    if (value.length === 3) {
                        value = value
                            .split('')
                            .map(s => s + s)
                            .join('')
                    }
                    if (value.length !== 6) {
                        value = 'FFFFFF'
                    }
                    hex = value
                    break
                }
                case 'object': {
                    hex = rgbToHex(value)
                }
            }
            let rgb
            try {
                rgb = hexToRgb(hex)
            } catch (error) {
                rgb = {
                    r: 255,
                    g: 255,
                    b: 255
                }
            }
            const { r, g, b } = rgb
            this._value = rgbToHex({ r, g, b }).toUpperCase()
            this.textInput.value = this._value
            this._colorBlock.style.backgroundColor = this.getValue()
            if (resetPosition) {
                const { h, s, b } = rgbToHsb(hex)
                this._height = 1 - h / 360
                if (h === 0) this._height = 0
                this._mouseX = s
                this._mouseY = 1 - b
            } else {
                if (this._lastValue !== this.value) {
                    this.onchange()
                }
            }
            this._lastValue = this.value
        }
        // updateValue(input = this.input) {
        //   if (input) input.value = this.getValue()
        //   this.textInput.value = this._value
        // }
        /**
         * @return {HTMLDivElement}
         */
        getDOM() {
            return this.dom
        }
        mouseBorder() {
            this._mouseX = numberBorder(
                this._mouseX / (this._gradientBlack.getBoundingClientRect().width - 2),
                1,
                0
            )
            this._mouseY = numberBorder(
                this._mouseY / (this._gradientBlack.getBoundingClientRect().height - 2),
                1,
                0
            )
        }
        mouseBorderBar() {
            this._height = numberBorder(
                this._height / (this._rightBar.getBoundingClientRect().height - 2),
                1,
                0
            )
        }
        updatePicker() {
            const position = this.position
            const target = this._gradientCircle
            target.style.left = `${position.x * 100}%`
            target.style.top = `${position.y * 100}%`
            this._rightBarPicker.style.top = `${this.height * 100}%`
            this._gradientColor.style.background = `linear-gradient(to right,#FFFFFF,#${rgbToHex(
                heightToRgb(this.height)
            )})`
            if (this.getBrightness() > 152) {
                addClassName(target, 'color-picker-circle-black')
                removeClassName(target, 'color-picker-circle-white')
            } else {
                removeClassName(target, 'color-picker-circle-black')
                addClassName(target, 'color-picker-circle-white')
            }
        }
        get position() {
            return {
                x: this._mouseX,
                y: this._mouseY
            }
        }
        get height() {
            return this._height
        }
        get value() {
            return this.getValue()
        }
        set value(value) {
            this.setValue(value, true)
            this.updatePicker()
        }
    }
    if (typeof exports === 'object') {
        // Node, CommonJSä¹‹ç±»çš„
        module.exports.ColorPicker = ColorPicker
    } else if (typeof window === 'object') {
        window.ColorPicker = ColorPicker
    }
})()