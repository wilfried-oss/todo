<?php include('parts/header.php'); ?>
<section class="col-lg-12 connectedSortable">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="ion ion-clipboard mr-1"></i>
                To Do List
            </h3>
            <div class="card-tools">
                <ul class="pagination pagination-sm">
                    <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                    <li class="page-item"><a href="#" class="page-link">1</a></li>
                    <li class="page-item"><a href="#" class="page-link">2</a></li>
                    <li class="page-item"><a href="#" class="page-link">3</a></li>
                    <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <ul id="todos" class="todo-list" data-widget="todo-list"></ul>
        </div>
    </div>
</section>
<div class="modal">
    <div class="modal-dialog">
        <div class="modal-content"></div>
    </div>
</div>
<?php include('parts/footer.php'); ?>
<script>
    $(function() {
        loadTodos();
        var Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 1000,
        });

        $("#todos").on("change", 'input[type="checkbox"]', function() {
            let step_id = $(this).attr("id");
            $.post(
                "data.php", {
                    step_id,
                },
                function() {
                    loadTodos();
                }
            );
            toastr.info("Mise à jour OK !");
        });

        $("#todos").on("change", 'input[checked="checked"]', function() {
            let step_id_toundo = $(this).attr("id");
            $.post(
                "data.php", {
                    step_id_toundo,
                },
                function() {
                    loadTodos();
                }
            );
            toastr.info("Mise à jour OK !");
        });

        $("#todos").on("click", ".fa-trash", function() {
            let todo_id = $(this).attr("id");
            $.post(
                "data.php", {
                    todo_id,
                },
                function() {
                    toastr.success("To Do bien supprimé !");
                    loadTodos();
                }
            );
        });

        $('#todos').on('click', 'button', function() {
            let todo_id = $(this).attr('id');
            let todo_title = $(this).attr('name');
            const modal_content = $(`
                <div class="modal-header">
                    <h4 class="modal-title">Add Step to ${todo_title}</h4>
                    <button type="button" class="close" style="color: red;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control form-control-border border-width-2" placeholder="Contenu">
                </div>
                <div class="modal-footer justify-content-between">
                    <button id="close" type="button" class="btn bg-gradient-danger" data-dismiss="modal">Close</button>
                    <button id="${todo_id}" type="button" class="add_step_button btn bg-gradient-primary">Save changes</button>
                </div>
            `);
            $('.modal-content').empty();
            $('.modal-content').append(modal_content);
            $('.modal').show();
        });

        $(".modal").on('click', '#close, .close', function() {
            $('.modal').hide();
        });

        $('.modal-content').on('click', '.add_step_button', function() {
            let todo_id = $(this).attr('id'),
                step_title = $('.modal-content input[class="form-control form-control-border border-width-2"]').val().trim();
            let add_step_to_todo = [todo_id, step_title];
            if (step_title !== '') {
                $.post('data.php', {
                    add_step_to_todo
                }, function(feedback) {
                    $('.modal').hide();
                    toastr.success("Etape bien ajoutée !");
                    loadTodos();
                });
            }
        });

        function clockClass() {
            let classTable = [
                "danger",
                "primary",
                "secondary",
                "success",
                "warning",
                "info",
                "dark",
                "light",
            ];
            let index = Math.floor(classTable.length * Math.random());
            return classTable[index];
        }

        function loadTodos() {
            $("#todos").empty();
            $.getJSON("data.php?all_todos", function(todos) {
                todos.forEach((todo) => {
                    let title_style = todo.all_done == 1 ? "line-through" : " ",
                        show_tools = todo.all_done == 1 ? " " : "hidden",
                        color = clockClass(),
                        badge_class = "badge" + " " + "badge-" + color,
                        add_btn_class = "btn bg-gradient-" + color;
                    const block = $(`
                        <li>
                            <span class="handle">
                                <i class="fas fa-ellipsis-v"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </span>
                            <div class="icheck-primary d-inline ml-2">
                                <label for="${"todoCheck" + todo.id}"></label>
                            </div>
                            <span class="text" style="text-decoration:${title_style}">
                                <a href="#">${todo.title}</a>
                            </span>
                            <small class="${badge_class}">
                                <i class="far fa-clock"></i>
                                ${todo.date_ajout}
                            </small>
                            <div class="tools">
                                <i ${show_tools} id="${todo.id}" class="fas fa-trash"></i>
                            </div>
                        </li>
                    `);
                    for (let i = 0; i < todo.steps.length; i++) {
                        let checkbox_attribut = todo.steps[i].done == 1 ? "checked" : "";
                        block.append(
                            $(`
                            <li class="mt-2 ml-5 form-check-label">${todo.steps[i].title}
                                <input id="${todo.steps[i].id}" ${checkbox_attribut} type="checkbox" 
                                class="ml-2 form-check-input">
                            </li>
                            `)
                        );
                    }
                    block.append(
                        $(`
                            <button id=${todo.id} name="${todo.title}" class="offset-11 ${add_btn_class} col-1"><i class="fas fa-plus"></i></button>
                        `)
                    );
                    $("#todos").append(block);
                });
            });
        }
    });
</script>
</body>

</html>