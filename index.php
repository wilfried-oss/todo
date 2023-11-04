<?php include('parts/header.php'); ?>
<div class="row">
	<section class="col-lg-5">
		<div class="card">
			<div class="card-header border-0">
				<h3 class="card-title">
					<i class="far fa-calendar-alt"></i>
					Add Todo
				</h3>
			</div>
			<div class="card-body pt-0">
				<div class="card-body mt-4">
					<label for="todo_title">ToDo Title</label>
					<input autofocus id="todo_title" type="text" class="form-control form-control-border" placeholder="Titre" />
					<br>
					<div class="form-group">
						<label class="mt-2" for="steps">Steps</label>
						<div id="steps">
							<div class="mt-2 row">
								<input id="step1" type="text" class="ml-2 form-control form-control-border col-10 step" placeholder="Step1" />
								<button id="ajouter_steps" type="button" class="ml-1 btn btn-outline-primary">
									+
								</button>
							</div>
						</div>
					</div>
					<button id="save_changes" class="btn btn-outline-primary col">
						Save changes
					</button>
				</div>
			</div>
		</div>
	</section>
	<section class="col-lg-7">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">
					<i class="ion ion-clipboard mr-1"></i>
					Last ToDos
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
</div>
</div>
<?php include('parts/footer.php'); ?>
<script>
	$(function() {
		loadTodos();
		let todo = {},
			todoTitle = "",
			todoStep1 = "",
			i = 1;
		var Toast = Swal.mixin({
			toast: true,
			position: "top-end",
			showConfirmButton: false,
			timer: 1000,
		});

		$("#ajouter_steps").on("click", function() {
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

		$("#steps").on("click", "a.btn-outline-danger", function() {
			$(this).parent().remove();
			toastr.info("Input bien supprimé !");
		});

		$("#save_changes").on("click", function() {
			var todoTitle = $("#todo_title").val().trim();
			var todoSteps = [];
			var isValid = true;

			// Parcourez les étapes et ajoutez-les à todoSteps
			$('#steps input[class="ml-2 form-control form-control-border col-10 step"]').each(function() {
				var stepValue = $(this).val().trim();
				if (stepValue !== '') {
					todoSteps.push(stepValue);
				} else {
					isValid = false; // Marquez comme invalide si une étape est vide
				}
			});

			// Vérifiez si tout est valide avant de faire la requête Ajax
			if (todoTitle && todoSteps.length > 0 && isValid) {
				var todo = {
					todoTitle,
					todoSteps,
				};

				$.post(
					"data.php", {
						todo,
					},
					function(response) {
						console.log(response);
						if (response == 1) toastr.success("Todo bien ajouté.");
						loadTodos();
					}
				);

				// Réinitialisez les champs
				$("#todo_title").val("");
				$('#steps input[class="ml-2 form-control form-control-border col-10 step"]').val("");
			}
		});

		$("#todos").on("change", 'input[type="checkbox"]', function() {
			let step_id = $(this).attr("id");
			$.post(
				"data.php", {
					step_id,
				},
				function() {
					toastr.info("Mise à jour OK !");
					loadTodos();
				}
			);
		});

		$("#todos").on("change", 'input[checked="checked"]', function() {
			let step_id_toundo = $(this).attr("id");
			$.post(
				"data.php", {
					step_id_toundo,
				},
				function() {
					toastr.info("Mise à jour OK !");
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
			$.getJSON("data.php?todos", function(todos) {
				todos.forEach((todo) => {
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
        					<span class="text">
        					  <a href="#">${todo.title}</a>
        					</span>
        					<small class="${badge_class}">
        					  <i class="far fa-clock"></i>
        					  ${todo.date_ajout}
        					</small>
        					<div class="tools">
        					  <i class="fas fa-trash">
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
					$("#todos").append(block);
				});
			});
		}
	});
</script>
</body>

</html>