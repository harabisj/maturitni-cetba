<template>
    <section class="row">
        <div class="col-lg-6">
            <h4>Díla k výběru</h4>
            
            <div class="accordion mb-3" id="booksAccordion">
                <div class="accordion-item" v-for="(group, gi) in groups" :key="gi">
                    <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" :data-bs-target="'#books-' + gi">
                        {{ group.title }}
                    </button>
                    </h2>
                    <div :id="'books-' + gi" class="accordion-collapse collapse" data-bs-parent="#booksAccordion">
                        <div class="accordion-body">
                            <ul class="list-unstyled mb-0">
                                <li v-for="(book, bi) in books.filter(b => b.group == gi)" :key="bi" class="d-flex justify-content-between align-items-center">
                                    <span :class="'text-' + (selection.filter(b => b.author == book.author).length < 2 ? 'primary' : 'muted' )" class="pointer mb-1" @click="addBook(book)">
                                        {{ book.author ? book.author + ' - ' + book.name : book.name }}
                                    </span>
                                    <span class="badge bg-primary small">
                                        {{ types[book.type] }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <h4>Literární druhy</h4>
            
            <div class="card mb-3">
                <div class="card-body row">
                    <div class="col-4 d-flex flex-column justify-cotnent-center align-items-center" v-for="(type, ti) in types" :key="ti">
                        <span
                            class="h3"
                            :class="'text-' + (getTypeSelection(ti).length < 2 ? 'danger' : 'success')"
                            :title="'Minimálně 2 díla druhu ' + type"
                        >
                            {{ getTypeSelection(ti).length }}x
                        </span>
                        <span>{{ type }}</span>
                    </div>
                </div>
            </div>
            
            <h4>Možnosti</h4>
            
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <input type="text" class="form-control" v-model="name" placeholder="Jméno a přijmení" @change="updateLocalMeta">
                </div>
                <div class="col-6 mb-3">
                    <input type="text" class="form-control" v-model="schoolclass" placeholder="Třída" @change="updateLocalMeta">
                </div>
                <div class="col-6">
                    <button class="btn btn-secondary w-100" @click="clear">Vymazat</button>
                </div>
                <div class="col-6">
                    <button class="btn w-100" :class="validateAll() ? 'btn-primary' : 'btn-secondary disabled'" @click="generate">Generovat</button>
                </div>
            </div>
            
            <p class="text-center text-muted small mb-3">
                Poslední aktualizace děl: {{ last_update }}
            </p>
        </div>
        
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Vybraná díla</h4>
                <h4 :class="'text-' + (this.selection.length == 20 ? 'success' : 'danger')">{{ selection.length }} / 20</h4>
            </div>
            
            <div class="card mb-3" v-for="(group, gi) in groups" :key="gi">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ group.title }}</h5>
                    <span
                        class="h5 mb-0"
                        :class="'text-' + (getGroupSelection(gi).length < group.minimum ? 'danger' : 'success')"
                        :title="'Minimálně ' + group.minimum + 'x dílo z období ' + group.title"
                    >
                        {{ getGroupSelection(gi).length }}
                    </span>
                </div>
                <div class="card-body">                    
                    <ul class="list-unstyled mb-0">
                        <li v-for="(book, bi) in getGroupSelection(gi)" :key="bi" class="d-flex justify-content-between align-items-center">
                            <span class="text-primary pointer mb-1" @click="removeBook(book)">
                                {{ book.author ? book.author + ' - ' + book.name : book.name }}
                            </span>
                            <span class="badge bg-primary small">
                                {{ types[book.type] }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div ref="pdf" style="display: none">
            <div style="font-family: 'Calibri'">
                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <h5 style="width: 60%; text-align: center">GYMNÁZIUM A STŘEDNÍ PRŮMYSLOVÁ ŠKOLA ELEKTROTECHNIKY A INFORMATIKY, FRENŠTÁT POD RADHOŠTĚM, PŘÍSPĚVKOVÁ ORGANIZACE</h5>
                    
                    <h3 style="margin-bottom: 1.5rem">Seznam literárních děl k ústní zkoušce z českého jazyka a literatury</h3>
                    
                    <div>Školní rok: <b>{{ (new Date).getFullYear() - 1 }} / {{ (new Date).getFullYear() }}</b></div>
                </div>
                <div style="display: flex; align-content: center; justify-content: space-around; margin: 1.75rem 0">
                    <div>
                        Jméno a přijmení: {{ name }}
                    </div>
                    <div>
                        Třída: {{ schoolclass }}
                    </div>
                </div>
                
                <div v-for="(group, gi) in groups" :key="gi">
                    <p><b><u>{{ group.title }}</u></b></p>
                    <ol :start="Object.keys(groups).filter(g => g < gi).map(g => getGroupSelection(g).length).reduce((a, b) => a + b, 0) + 1">
                        <li v-for="(book, bi) in getGroupSelection(gi)" :key="bi">
                            {{ book.author ? book.author + ' - ' + book.name : book.name }}
                        </li>
                    </ol>
                </div>
                
                Ve Frenštátě pod Radhoštěm dne 31. března {{ (new Date).getFullYear() }}
            </div>
        </div>
    </section>
</template>

<script>
import { groups, types, books, last_update } from '../data.js';

export default {
    data: function () {
        return {
            selection: [],
            groups: groups,
            types: types,
            books_import: books,
            books: books,
            last_update: last_update,
            name: null,
            schoolclass: null,
        };
    },
    mounted() {
        selectionif: if (localStorage.selection) {
            var lsSelection = JSON.parse(localStorage.selection);
            if (lsSelection === null || !lsSelection.length)
                break selectionif;
            
            this.books = this.books
                .filter(b => !lsSelection.map(bs => bs.author + ',' + bs.name).includes(b.author + ',' + b.name));
            this.selection = lsSelection;
        }
        
        if (localStorage.name)
            this.name = localStorage.name;
            
        if (localStorage.schoolclass)
            this.schoolclass = localStorage.schoolclass;
    },
    methods: {
        addBook(book) {
            if (this.selection.filter(b => b.author == book.author).length >= 2)
                return;
            
            this.books = this.books
                .filter(b => b != book);
                
            this.selection
                .push(book);
                
            localStorage.selection = JSON.stringify(this.selection);
        },
        removeBook(book) {
            this.selection = this.selection
                .filter(b => b != book);
                
            this.books
                .push(book);
                
            localStorage.selection = JSON.stringify(this.selection);
        },
        getGroupSelection(group_id) {
            return this.selection
                .filter(b => b.group == group_id);
        },
        getTypeSelection(type_id) {
            return this.selection
                .filter(b => b.type == type_id);
        },
        validateGroups() {
            return Object.keys(this.groups)
                .filter(gi => this.getGroupSelection(gi).length < this.groups[gi].minimum)
                .length;
        },
        validateTypes() {
            return Object.keys(this.types)
                .filter(ti => this.getTypeSelection(ti).length < 2)
                .length;
        },
        validateAll() {
            return this.selection.length == 20 &&
                  !this.validateGroups() &&
                  !this.validateTypes() &&
                  !Object.values(this.groupBy(this.selection, 'author')).filter(a => a.length > 2).length;
        },
        clear() {
            this.selection = [];
            localStorage.selection = null;
            this.books = this.books_import;
        },
        generate() {
            let w = window.open()
            w.document.write(this.$refs.pdf.innerHTML)
            w.document.close()
            w.setTimeout(function () {
                w.print()
            }, 1000);
        },
        updateLocalMeta() {
            localStorage.name = this.name;
            localStorage.schoolclass = this.schoolclass;
        },
        groupBy(xs, key) {
            return xs.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {});
        },
    },
}
</script>

<style scoped>
.pointer {
    cursor: pointer;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none; 
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.accordion-button.collapsed, .accordion-button:not(.collapsed) {
    background-color: #5B6977;
    color: #ebebeb;
}

.accordion-button:not(.collapsed)::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ebebeb'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-body {
    background-color: #4e5d6c;
}
</style>