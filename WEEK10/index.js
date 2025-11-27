/* ==========================================================
   CASIO fx-580VN X — Math Input Engine (Step 1 & 2)
   Modules:
   - Token Engine
   - Math Layout Engine (Fraction, Root, Power)
   ========================================================== */

/* =======================
   DOM elements
========================= */
const mainDisplay = document.getElementById("main-display");
const subDisplay = document.getElementById("sub-display");

/* ==========================================================
   1. TOKEN ENGINE
   ----------------------------------------------------------
   Chuyển từng key bấm thành token để Math Engine xử lý.
   ========================================================== */

let tokens = [];        // Mảng chứa token
let cursor = 0;         // Vị trí con trỏ Math Input

// Hàm thêm token tại vị trí cursor
function insertToken(tk) {
    tokens.splice(cursor, 0, tk);
    cursor++;
    updateDisplay();
}

/* ==========================================================
   2. MATH LAYOUT ENGINE (hiển thị dạng đẹp)
   ----------------------------------------------------------
   Những ký hiệu cần Math Layout:
   - POWER: a^b
   - ROOT: √(...)
   - FRACTION: a/b dạng 2 tầng
   ========================================================== */

function renderMath(tokens) {
    let html = "";

    for (let t of tokens) {

        // Phân số dạng đẹp
        if (t.type === "fraction") {
            html += `
            <span class="frac">
                <span class="top">${renderMath(t.numer)}</span>
                <span class="bottom">${renderMath(t.denom)}</span>
            </span>`;
        }

        // Căn bậc 2
        else if (t.type === "sqrt") {
            html += `
            <span class="sqrt">
                √<span class="radicand">${renderMath(t.value)}</span>
            </span>`;
        }

        // Lũy thừa
        else if (t.type === "power") {
            html += `
            <span class="power">
                ${renderMath(t.base)}<sup>${renderMath(t.exp)}</sup>
            </span>`;
        }

        // Token thường (số, dấu, sin, cos…)
        else {
            html += t.value;
        }
    }

    return html;
}

/* ==========================================================
   Update LCD
========================================================== */
function updateDisplay() {
    if (tokens.length === 0) {
        mainDisplay.innerHTML = "0";
    } else {
        mainDisplay.innerHTML = renderMath(tokens);
    }

    // sub-display (biểu thức gốc dạng linear)
    subDisplay.innerHTML = tokens.map(t => t.raw || t.value).join("");
}

/* ==========================================================
   KEYBOARD HANDLER
   (Mỗi nút bấm → sinh token tương ứng)
========================================================== */

function handleKey(key) {
    /* ===========================
       Number keys
       =========================== */
    if (!isNaN(key) || key === ".") {
        insertToken({type: "num", value: key});
        return;
    }

    /* ===========================
       Operators
       =========================== */
    if (["+", "-", "×", "÷", "^"].includes(key)) {
        insertToken({type: "op", value: key});
        return;
    }

    /* ===========================
       sin, cos, tan, ln, log
       =========================== */
    if (["sin", "cos", "tan", "ln", "log"].includes(key)) {
        insertToken({type: "func", value: key + "("});
        return;
    }

    /* ===========================
       Parentheses
       =========================== */
    if (key === "(" || key === ")") {
        insertToken({type: "paren", value: key});
        return;
    }

    /* ===========================
       Square Root
       =========================== */
    if (key === "√") {
        insertToken({
            type: "sqrt",
            value: []
        });
        return;
    }

    /* ===========================
       Power x^y
       =========================== */
    if (key === "^") {
        insertToken({
            type: "power",
            base: [{type: "placeholder"}],
            exp: [{type: "placeholder"}]
        });
        return;
    }

    /* ===========================
       Fraction (SHIFT + ÷)
       =========================== */
    if (key === "frac") {
        insertToken({
            type: "fraction",
            numer: [],
            denom: []
        });
        return;
    }

    /* ===========================
       AC — reset
       =========================== */
    if (key === "AC") {
        tokens = [];
        cursor = 0;
        updateDisplay();
        return;
    }

    /* ===========================
       DEL — delete previous token
       =========================== */
    if (key === "DEL") {
        if (cursor > 0) {
            tokens.splice(cursor - 1, 1);
            cursor--;
            updateDisplay();
        }
        return;
    }

    /* ===========================
       Equal (=) — bước 3 (engine)
       =========================== */
    if (key === "=") {
        // Sẽ làm ở Bước 3
        return;
    }
}

/* ==========================================================
   EVENT LISTENER CHO TOÀN BỘ NÚT
========================================================== */
document.querySelectorAll("button").forEach(btn => {
    btn.addEventListener("click", () => {
        const key = btn.dataset.key;
        handleKey(key);
    });
});

/* First render */
updateDisplay();
/* ==========================================================
   3. EXPRESSION EVALUATOR — CASIO Math Engine
   ========================================================== */

/* Convert tokens → linear expression string */
function toLinear(tokens) {
    let out = "";

    for (let t of tokens) {

        if (t.type === "num") out += t.value;

        else if (t.type === "op") {
            if (t.value === "×") out += "*";
            else if (t.value === "÷") out += "/";
            else out += t.value;
        }

        else if (t.type === "paren") out += t.value;

        else if (t.type === "func") out += t.value;  // sin( , log( ...

        /* Fraction = (numer)/(denom) */
        else if (t.type === "fraction") {
            out += "(" + toLinear(t.numer) + ")/(" + toLinear(t.denom) + ")";
        }

        /* Square root = sqrt(...) */
        else if (t.type === "sqrt") {
            out += "Math.sqrt(" + toLinear(t.value) + ")";
        }

        /* Power = base^(exp) */
        else if (t.type === "power") {
            out += "Math.pow(" + toLinear(t.base) + "," + toLinear(t.exp) + ")";
        }
    }
    return out;
}

/* Safe Evaluate */
function evaluateExpression() {
    try {
        let expr = toLinear(tokens);

        // Replace Casio functions with JavaScript equivalents
        expr = expr.replace(/sin\(/g, "Math.sin(")
                   .replace(/cos\(/g, "Math.cos(")
                   .replace(/tan\(/g, "Math.tan(")
                   .replace(/log\(/g, "Math.log10(")
                   .replace(/ln\(/g, "Math.log(");

        let result = eval(expr);

        if (isNaN(result)) return "Error";

        return result;
    }
    catch (e) {
        return "Error";
    }
}

/* Press "=" → compute */
function compute() {
    let result = evaluateExpression();

    mainDisplay.innerHTML = result;
    subDisplay.innerHTML = "";

    // Reset for next expression
    tokens = [{type: "num", value: String(result)}];
    cursor = 1;
}

/* Bind "=" key */
document.querySelector('[data-key="="]').addEventListener("click", compute);
