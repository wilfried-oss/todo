$(function () {
  loadTodos();
  let todo = {},
    todoTitle = "",
    todoStep1 = "",
    todoSteps = [],
    i = 1;
  var Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 1000,
  });
  $("#ajouter_steps").on("click", function () {
    i++;
    const div = $(`
        <div class="row">
            <input class="ml-2 form-control form-control-border col-10 step" placeholder="${
              "Step" + i
            }">
            <a href="javascript:void(0)" type="button" class="ml-1 btn btn-outline-danger">x</a>
        </div>				
    `);
    $("#steps").append(div);
  });

  $("#steps").on("click", "a.btn-outline-danger", function () {
    $(this).parent().remove();
    toastr.info("Input bien supprimé !");
  });

  $("#save_changes").on("click", function () {
    todoTitle = $("#todo_title").val().trim();
    $(
      '#steps input[class="ml-2 form-control form-control-border col-10 step"]'
    ).each(function () {
      todoSteps.push($(this).val().trim());
    });
    todo = {
      todoTitle,
      todoSteps,
    };
    if (todo) {
      $.post(
        "data.php",
        {
          todo,
        },
        function (response) {
          console.log(response);
          if (response == 1) toastr.success("Todo bien ajouté.");
          loadTodos();
        }
      );
      $("#todo_title").val(" ");
      $(
        '#steps input[class="ml-2 form-control form-control-border col-10 step"]'
      ).val(" ");
    }
  });

  $("#todos").on("change", 'input[type="checkbox"]', function () {
    let step_id = $(this).attr("id");
    $.post(
      "data.php",
      {
        step_id,
      },
      function () {
        toastr.info("Mis à jour OK !");
        loadTodos();
      }
    );
  });

  $("#todos").on("change", 'input[checked="checked"]', function () {
    let step_id_toundo = $(this).attr("id");
    $.post(
      "data.php",
      {
        step_id_toundo,
      },
      function () {
        toastr.info("Mis à jour OK !");
        loadTodos();
      }
    );
  });

  $("#todos").on("click", ".fa-trash", function () {
    let todo_id = $(this).attr("id");
    $.post(
      "data.php",
      {
        todo_id,
      },
      function () {
        toastr.success("To Do bien supprimé !");
        loadTodos();
      }
    );
  });

  function clockClass() {
    let classTable = [
      "badge-danger",
      "badge-primary",
      "badge-secondary",
      "badge-success",
      "badge-warning",
      "badge-info",
      "badge-dark",
      "badge-light",
    ];
    let index = Math.floor(classTable.length * Math.random());
    return classTable[index];
  }

  function loadTodos() {
    $("#todos").empty();
    $.getJSON("data.php?todos", function (todos) {
      todos.forEach((todo) => {
        let title_style = todo.all_done == 1 ? "line-through" : " ",
          show_tools = todo.all_done == 1 ? " " : "hidden",
          badge_class = "badge" + " " + clockClass();

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
              <i ${show_tools} id="${todo.id}" class="fas fa-trash">
              </i>
            </div>
          </li>
        `);

        for (let i = 0; i < todo.steps.length; i++) {
          let checkbox_attribut = todo.steps[i].done == 1 ? "checked" : "";
          block.append(
            $(`
            <li class="mt-2 ml-5 form-check-label">${todo.steps[i].title}
                  <input id="${todo.steps[i].id}" ${checkbox_attribut} type="checkbox"class="ml-2 form-check-input">
            </li>
            `)
          );
        }
        block.append($(`<li class="ml-5">-----</li>`));
        $("#todos").append(block);
      });
    });
  }
});
